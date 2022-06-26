<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;

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
		} catch (InvalidPathException|NotFoundException|NotPermittedException $e) {
			return false;
		}
		return true;
	}
}
