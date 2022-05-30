<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * Returns the node object for the user's root directory.
 */
class Root extends RegistrableFunction {
	public function getCallback($node=null): array {
		return $this->getNodeData($this->getRootFolder());
	}
}
