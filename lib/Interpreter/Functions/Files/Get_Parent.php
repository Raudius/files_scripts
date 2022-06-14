<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * Returns the parent path for the given file or directory.
 * The root of the "filesystem" is considered to be the root directory of the user who triggered the script.
 * When attempting to get the parent of the root directory, the root directory is returned.
 *
 * If the passed node cannot be found, null is returned.
 */
class Get_Parent extends RegistrableFunction {
	public function getCallback($node=null): ?array {
		$node = $this->getNode($this->getPath($node));
		if (!$node) {
			return null;
		}
		try {
			if ($node->getId() === $this->getRootFolder()->getId()) {
				$node = $this->getRootFolder();
			}
		} catch (InvalidPathException|NotFoundException $e) {
			return null;
		}

		return $this->getNodeData($node->getParent());
	}
}
