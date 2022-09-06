<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\NotFoundException;

/**
 * `full_path(Node node): String|nil`
 *
 * Returns the full path of the given file or directory including the node's name.
 * *Example:* for a file `abc.txt` in directory `/path/to/file` the full path is: `/path/to/file/abc.txt`.
 *
 * If the file does not exist `nil` is returned.
 */
class Full_Path extends RegistrableFunction {
	public function run($node = null): ?string {
		$node = $this->getNode($this->getPath($node));
		try {
			return $node ? $this->getHomeFolder()->getRelativePath($node->getPath()) : null;
		} catch (NotFoundException $e) {
			return null;
		}
	}
}
