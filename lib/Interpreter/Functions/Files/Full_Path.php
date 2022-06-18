<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * Returns the full path of the given file or directory.
 * The full path in this case is the relative path from the user's root directory, and includes the node's name.
 * E.g. for a file `abc.txt` in directory `/path/to/file` the full path is: `/path/to/file/abc.txt`.
 */
class Full_Path extends RegistrableFunction {
	public function run($node=null): ?string {
		$node = $this->getNode($this->getPath($node));
		return $node ? $this->getRootFolder()->getRelativePath($node->getPath()) : null;
	}
}
