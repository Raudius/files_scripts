<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Constants;
use OCP\IUserSession;
use OCP\IUserManager;
use OCP\Share\IManager;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Share\IShare;

/**
 * `tag_file(Node file, Tag tag): Bool`
 *
 * TODO FIXME add docs
 */
class Share_File_With_User extends RegistrableFunction {
	use ShareSerializerTrait;
	use UserSerializerTrait;

	private IManager $shareManager;
	private IUserSession $userSession;
	private IUserManager $userManager;

	public function __construct(IUserSession $userSession, IManager $shareManager, IUserManager $userManager) {
		$this->shareManager = $shareManager;
		$this->userSession = $userSession;
		$this->userManager = $userManager;
	}

	public function run($nodeData=[], $tagerUserData=[], $permissions=null): ?array {
		$user = $this->userSession->getUser();
		$targetUser = $this->deserializeUser($tagerUserData, $this->userManager);
		$node = $this->getNode($this->getPath($nodeData));

		if ($user === null || $node === null || $targetUser == null) {
			return [];
		}

		// Default permission is READ-ONLY
		$permissions = is_integer($permissions) ? $permissions : Constants::PERMISSION_READ;

		$share = $this->shareManager->newShare()
			->setNode($node)
			->setShareType(IShare::TYPE_USER)
			->setPermissions($permissions)
			->setSharedBy($user->getUID())
			->setSharedWith($targetUser->getUID());

		try {
			$share = $this->shareManager->createShare($share);
			return $this->serializeShare($share);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
