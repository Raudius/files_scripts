<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;
use OCP\Files\NotPermittedException;

/**
 * Creates a new file and returns the file node.
 * If file creation fails, returns null
 */
class New_File extends RegistrableFunction {
	public function getCallback($node=null, $name=null, $content=null): ?array {
		$folder = $this->getNode($this->getPath($node));
		if (false === $folder instanceof Folder) {
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
