<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;
use OCP\Files\NotPermittedException;

/**
 * `new_file(Node folder, String name, [String content]=nil): Node|nil`
 *
 * Creates a new file at specified folder.
 * If successful, returns the newly created file node. If file creation fails, returns `nil`.
 */
class New_File extends RegistrableFunction {
	public function run($node = null, $name = null, $content = null): ?array {
		$folder = $this->getNode($this->getPath($node));
		if (false === $folder instanceof Folder || !$name) {
			return null;
		}

		try {
			$file = $folder->newFile($name, $content);
			return $this->getNodeData($file);
		} catch (NotPermittedException $e) {
			return null;
		}
	}
}
