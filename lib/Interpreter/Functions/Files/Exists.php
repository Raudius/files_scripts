<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * exists(Node node, String file_name=nil)
 *
 * Returns whether a file or directory exists.
 * Optionally the name of a file can be specified as a second argument, in which case the first argument will be
 * assumed to be directory. The function will return whether there is such file in the directory.
 */
class Exists extends RegistrableFunction {
	public function getCallback(?array $node=[], string $fileName=null): bool {
		if (!$node) {
			return false;
		}

		$path = $this->getPath($node);
		if ($fileName === null) {
			return $this->getRootFolder()->nodeExists($path);
		}

		$folderNode = $this->getFolder($path);
		if (!$folderNode) {
			return false;
		}

		return $folderNode->nodeExists($fileName);
	}
}
