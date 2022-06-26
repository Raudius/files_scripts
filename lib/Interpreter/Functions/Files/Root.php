<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `root(): Node`
 *
 * Returns the node object for the user's root directory.
 */
class Root extends RegistrableFunction {
	public function run($node = null): array {
		return $this->getNodeData($this->getRootFolder());
	}
}
