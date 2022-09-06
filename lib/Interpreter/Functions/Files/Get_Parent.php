<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * `get_parent(Node node): Node`
 *
 * Returns the parent folder for the given file or directory.
 * The root of the "filesystem" is considered to be the home directory of the user who is running the script. When attempting to get the parent of the root directory, the root directory is returned.
 *
 * If the given file cannot be found, `nil` is returned.
 */
class Get_Parent extends RegistrableFunction {
	public function run($node = null): ?array {
		$node = $this->getNode($this->getPath($node));
		if (!$node) {
			return null;
		}
		try {
			if ($node->getId() === $this->getHomeFolder()->getId()) {
				$node = $this->getHomeFolder();
			}
		} catch (InvalidPathException|NotFoundException $e) {
			return null;
		}

		return $this->getNodeData($node->getParent());
	}
}
