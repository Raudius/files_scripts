<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\IUserSession;
use OCP\Share\IManager;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `share_find(Table parameters): Share[]`
 *
 * TODO FIXME add docs
 */
class Share_Delete extends RegistrableFunction {
	use ShareSerializerTrait;

	private IManager $shareManager;

	public function __construct(IUserSession $userSession, IManager $shareManager) {
		$this->shareManager = $shareManager;
	}

	public function run($shareData = []): bool{
		$share = $this->deserializeShare($shareData, $this->shareManager);
		if ($share === null) {
			return false;
		}

		try {
			$this->shareManager->deleteShare($share);
			return true;
		} catch (\Throwable $e) {
			return false;
		}
	}
}
