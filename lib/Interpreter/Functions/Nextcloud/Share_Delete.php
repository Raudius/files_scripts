<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Share\IManager;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `share_delete(Share share): Bool
 *
 * Deletes the share, returns whether the deletion succeeded.
 */
class Share_Delete extends RegistrableFunction {
	use ShareSerializerTrait;

	private IManager $shareManager;

	public function __construct(IManager $shareManager) {
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
