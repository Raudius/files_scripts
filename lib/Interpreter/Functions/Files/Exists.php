<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `exists(Node node, [String file_name]=nil): Bool`
 *
 * Returns whether a file or directory exists.
 * Optionally the name of a file can be specified as a second argument, in which case the first argument will be assumed to be directory. The function will return whether the file exists in the directory.
 */
class Exists extends RegistrableFunction {
	public function run($node = [], $fileName = null): bool {
		if (!$node) {
			return false;
		}

		$path = $this->getPath($node);
		if ($fileName === null) {
			return $this->getHomeFolder()->nodeExists($path);
		}

		$folderNode = $this->getFolder($path);
		if (!$folderNode) {
			return false;
		}

		return $folderNode->nodeExists($fileName);
	}
}
