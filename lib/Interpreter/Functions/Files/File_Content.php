<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\NotPermittedException;
use OCP\Lock\LockedException;

/**
 * `file_content(Node node): String|nil`
 *
 * Returns the string content of the file. If the node is a directory or the file does not exist, `nil` is returned.
 */
class File_Content extends RegistrableFunction {
	public function run($node = null): ?string {
		$node = $this->getFile($this->getPath($node));
		if (!$node) {
			return null;
		}

		try {
			return $node->getContent();
		} catch (LockedException|NotPermittedException $e) {
			return null;
		}
	}
}
