<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Lock\ILockingProvider;

/**
 * `file_unlock(Node node, [Bool success_if_not_found]=true): Bool`
 *
 * Lifts a file lock from the specified file/folder node.
 * Returns whether operation succeeded.
 *
 * By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.
 */
class File_Unlock extends RegistrableFunction {
	public function run($node = [], $successIfNotFound = true): bool {
		$file = null;
		try {
			$file = $this->getNode($this->getPath($node));
		} catch (\Throwable $e) {
		}

		if (!$file) {
			return (bool) $successIfNotFound;
		}

		try {
			$file->unlock(ILockingProvider::LOCK_SHARED);
			$file->unlock(ILockingProvider::LOCK_EXCLUSIVE);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
}
