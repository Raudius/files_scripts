<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `file_delete(Node node, [Bool success_if_not_found]=true): Bool`
 *
 * Deletes the specified file/folder node.
 * Returns whether deletion succeeded.
 *
 * By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.
 */
class File_Delete extends RegistrableFunction {
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
			$file->delete();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
}
