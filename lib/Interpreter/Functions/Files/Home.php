<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `home(): Node`
 *
 * Returns the node object for the user's home directory.
 */
class Home extends RegistrableFunction {
	public function run($node = null): array {
		return $this->getNodeData($this->getHomeFolder());
	}
}
