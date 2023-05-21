<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Lock\ILockingProvider;

/**
 * `file_lock(Node node, [Int lock_type]="shared"): Bool`
 *
 * Locks thethe specified file/folder node. Returns whether operation succeeded.
 *
 * By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.
 */
class File_Lock extends RegistrableFunction {
	public function run($node = [], $lockType='shared'): bool {
		$file = null;
		try {
			$file = $this->getNode($this->getPath($node));
		} catch (\Throwable $e) {
			return false;
		}

		$lockTypeValue = ILockingProvider::LOCK_SHARED;
		if ($lockType === 'exclusive') {
			$lockTypeValue = ILockingProvider::LOCK_EXCLUSIVE;
		}

		try {
			$file->lock($lockTypeValue);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
}
