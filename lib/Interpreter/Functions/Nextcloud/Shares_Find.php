<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Files\Folder;
use OCP\IUserSession;
use OCP\Share\IManager;
use OCP\Share\IShare;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use Psr\Log\LoggerInterface;

/**
 * `share_find(Table parameters): Share[]`
 *
 * TODO FIXME add docs
 */
class Shares_Find extends RegistrableFunction {
	use ShareSerializerTrait;

	const ALL_SHARE_TYPES = [
		IShare::TYPE_USER,
		IShare::TYPE_GROUP,
		IShare::TYPE_LINK,
		IShare::TYPE_REMOTE,
		IShare::TYPE_EMAIL,
		IShare::TYPE_ROOM,
		IShare::TYPE_CIRCLE,
		IShare::TYPE_DECK,
		IShare::TYPE_SCIENCEMESH,
	];

	private IManager $shareManager;
	private IUserSession $userSession;

	public function __construct(IUserSession $userSession, IManager $shareManager) {
		$this->userSession = $userSession;
		$this->shareManager = $shareManager;
	}

	public function run($nodeData = [], $shareTypes = null): array {
		$user = $this->userSession->getUser();
		$node = $this->getNode($this->getPath($nodeData));
		if ($user === null || $node === null) {
			return [];
		}

		$shareTypes = is_array($shareTypes) ? $shareTypes : self::ALL_SHARE_TYPES;
		$shareObjects = [];
		foreach ($shareTypes as $shareType) {
			$shares = $this->shareManager->getSharesBy($user->getUID(), $shareType, $node, false, -1, 0);

			foreach ($shares as $share) {
				$shareObjects[] = $this->serializeShare($share);
			}
		}
		return $shareObjects;
	}
}
