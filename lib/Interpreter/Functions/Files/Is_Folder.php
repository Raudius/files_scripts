<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;

/**
 * `is_folder(Node node): Bool`
 *
 * Returns whether the given node is a folder.
 */
class Is_Folder extends RegistrableFunction {
	public function run($node = []): bool {
		return $this->getNode($this->getPath($node)) instanceof Folder;
	}
}
