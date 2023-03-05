<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;
use OCP\Files\NotPermittedException;

/**
 * `new_folder(Node parent, String name): Node|nil`
 *
 * Creates a new folder at the specified parent folder.
 * If successful, returns the newly created folder node. If creation fails, returns `nil`.
 */
class New_Folder extends RegistrableFunction {
	public function run($node = null, $name = null): ?array {
		$folder = $this->getNode($this->getPath($node));
		if (false === $folder instanceof Folder || !$name) {
			return null;
		}

		try {
			$file = $folder->newFolder($name);
			return $this->getNodeData($file);
		} catch (NotPermittedException $e) {
			return null;
		}
	}
}
