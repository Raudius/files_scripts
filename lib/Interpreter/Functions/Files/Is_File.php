<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\File;

/**
 * `is_file(Node node): Bool`
 *
 * Returns whether the given node is a file.
 */
class Is_File extends RegistrableFunction {
	public function run($node = []): bool {
		return $this->getNode($this->getPath($node)) instanceof File;
	}
}
