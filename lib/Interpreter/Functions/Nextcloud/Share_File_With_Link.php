<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Constants;
use OCP\IUserSession;
use OCP\Share\IManager;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Share\IShare;

/**
 * `tag_file(Node file, Tag tag): Bool`
 *
 * TODO FIXME add docs
 */
class Share_File_With_Link extends RegistrableFunction {
	use ShareSerializerTrait;

	private IManager $shareManager;
	private IUserSession $userSession;

	public function __construct(IUserSession $userSession, IManager $shareManager,) {
		$this->shareManager = $shareManager;
		$this->userSession = $userSession;
	}

	public function run($nodeData=[], $permissions=null, $password=null): ?array {
		$user = $this->userSession->getUser();
		$node = $this->getNode($this->getPath($nodeData));

		$hasValidPassword = is_string($password) || $password === null;
		if ($user === null || $node === null || !$hasValidPassword) {
			return [];
		}

		// Default permission is READ-ONLY
		$permissions = is_integer($permissions) ? $permissions : Constants::PERMISSION_READ;

		$share = $this->shareManager->newShare()
			->setShareType(IShare::TYPE_LINK)
			->setNode($node)
			->setPermissions($permissions)
			->setSharedBy($user->getUID());

		if ($password) {
			$share->setPassword($password);
		}

		try {
			$share = $this->shareManager->createShare($share);
			return $this->serializeShare($share);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
