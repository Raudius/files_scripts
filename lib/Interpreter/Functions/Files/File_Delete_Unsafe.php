<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\IRootFolder;

/**
 * `file_delete_unsafe(String path, [Bool success_if_not_found]=true): Bool`
 *
 * Deletes a file/folder node in the given path.
 * By default, the function also returns `true` if the file was not found. This behaviour can be changed by setting its second argument to `false`.
 *
 * ⚠️ Use of this function is strongly discouraged as it may allow users to delete files from other users.
 */
class File_Delete_Unsafe extends RegistrableFunction {
	private IRootFolder $root;

	public function __construct(IRootFolder $root) {
		$this->root = $root;
	}

	public function run($path = null, $successIfNotFound = true): bool {
		try {
			$file = $this->root->get($path);
		} catch (\Exception|\Throwable $e) {
			$file = null;
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
