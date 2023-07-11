<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Constants;
use OCP\IUserSession;
use OCP\IUserManager;
use OCP\Share\IManager;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Share\IShare;

/**
 * `share_file(Node file, Table options): Share|nil`
 *
 * Creates a share for the given file with the configuration options given.
 *
 * The configuration table may contain the following properties:
 *  - `target`: The target of the share, may be a user object (see: [users_find](#users_find)), or the constant `SHARE_TARGET_LINK` may be used to create a link share.
 *  - `expiration`: An expiration date for the share (see: [create_date_time](#create_date_time))
 *  - `hide_download`: Whether the download button should be hidden in public (link) shares
 *  - `permissions`: The [permissions](#Permissions) for the shared file, these can be combined with the bitwise-or operator `|`.
 *  - `label`: A label to attach to the share
 *  - `password`: A password with which to protect the share.
 *  - `note`: A note to attach to the share
 *  - `token`: The share token (used in public share URL: `index.php/s/<share-token>`
 *
 * Examples:
 * ```lua
 * file = get_input_files()[1]
 *
 * -- Share the file with user with UID="alice", allow to read and modify only
 * found_users = users_find(nil, "alice")
 * alice = found_users[1]
 *
 * share_file(file, {
 *   target= alice,
 *   permissions= PERMISSION_READ | PERMISSION_UPDATE
 * })
 *
 *
 * -- Share the file via a public link
 * share_file(file, {
 *   target= SHARE_TARGET_LINK,
 *   expiration= create_date_time(2025, 06, 07), -- 7th June 2025
 *   password= "hunter2",
 *   token= "makes-url-pretty"
 * })
 * ```
 */
class Share_File extends RegistrableFunction {
	use ShareSerializerTrait;
	use UserSerializerTrait;

	const SHARE_TARGET_LINK = "share_link";
	const OPTION_KEY_PERMISSIONS = "permissions";
	const OPTION_KEY_TARGET = "target";
	const OPTION_KEY_PASSWORD = "password";
	const OPTION_KEY_LABEL = "label";
	const OPTION_KEY_EXPIRATION = "expiration";
	const OPTION_KEY_HIDE_DOWNLOAD = "hide_download";
	const OPTION_KEY_NOTE = "note";
	const OPTION_KEY_TOKEN = "token";

	private IManager $shareManager;
	private IUserSession $userSession;
	private IUserManager $userManager;

	public function __construct(IUserSession $userSession, IManager $shareManager, IUserManager $userManager) {
		$this->shareManager = $shareManager;
		$this->userSession = $userSession;
		$this->userManager = $userManager;
	}

	public function run($nodeData=[], $shareData=[]): ?array {
		$user = $this->userSession->getUser();
		$node = is_array($nodeData) ? $this->getNode($this->getPath($nodeData)) : null;

		if ($user === null || $node === null || !is_array($shareData)) {
			return [];
		}

		$permissions = $this->getPermissions($shareData);
		[$shareType, $shareTargetId] = $this->getShareTarget($shareData);
		if ($shareType === null) {
			return [];
		}

		// Create share object
		$share = $this->shareManager->newShare()
			->setSharedBy($user->getUID())
			->setNode($node)
			->setShareType($shareType)
			->setPermissions($permissions);

		// Set optional parameters
		$password = $this->getPassword($shareData);
		$password && $share->setPassword($password);

		$label = $this->getLabel($shareData);
		$label && $share->setLabel($label);

		$expiration = $this->getExpiration();
		$expiration && $share->setExpirationDate($expiration);

		$hideDownload = $this->getHideDownload();
		($hideDownload !== null) && $share->setHideDownload($hideDownload);

		$note = $this->getNote();
		$note && $share->setNote($note);

		$token = $this->getToken();
		$token && $share->setToken($token);

		$shareTargetId && $share->setSharedWith($shareTargetId);

		// Try to create share from object
		try {
			$share = $this->shareManager->createShare($share);
			return $this->serializeShare($share, $this->getHomeFolder());
		} catch (\Throwable $e) {
			return null;
		}
	}

	protected function getConstants(): array {
		return [
			'SHARE_TARGET_LINK' => self::SHARE_TARGET_LINK
		];
	}

	private function getPermissions(array $shareData): int {
		$permissions = $shareData[self::OPTION_KEY_PERMISSIONS] ?? null;
		// Default permission is READ-ONLY
		$permissions = is_integer($permissions) ? $permissions : Constants::PERMISSION_READ;
		return $permissions;
	}

	/**
	 * @param array $shareData
	 * @return array
	 */
	private function getShareTarget(array $shareData): array {
		$shareTarget = $shareData[self::OPTION_KEY_TARGET] ?? null;
		if ($shareTarget === self::SHARE_TARGET_LINK) {
			return [IShare::TYPE_LINK, null];
		}

		if (!is_array($shareTarget)) {
			return [null, null];
		}

		$targetUser = $this->deserializeUser($shareTarget, $this->userManager);
		if ($targetUser) {
			return [IShare::TYPE_USER, $targetUser->getUID()];
		}

		return [null, null];
	}

	private function getPassword(array $shareData): ?string {
		$password = $shareData[self::OPTION_KEY_PASSWORD] ?? null;
		return is_string($password) ? $password : null;
	}

	private function getLabel(array $shareData): ?string {
		$label = $shareData[self::OPTION_KEY_LABEL] ?? null;
		return is_string($label) ? $label : null;
	}

	private function getExpiration(): ?\DateTime {
		$expiration = $shareData[self::OPTION_KEY_EXPIRATION] ?? null;
		return is_array($expiration) ? $this->unpackDate($expiration) : null;
	}

	private function getHideDownload(): ?bool {
		$hideDownload = $shareData[self::OPTION_KEY_HIDE_DOWNLOAD] ?? null;
		return is_bool($hideDownload) ? $hideDownload : null;
	}

	private function getNote(): ?string {
		$note = $shareData[self::OPTION_KEY_NOTE] ?? null;
		return is_string($note) ? $note : null;
	}

	private function getToken(): ?string {
		$token = $shareData[self::OPTION_KEY_TOKEN] ?? null;
		return is_string($token) ? $token : null;
	}
}
