<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;

/**
 *
 */
class Is_Folder extends RegistrableFunction {
	public function run(?array $node=[]): bool {
		return $this->getNode($this->getPath($node)) instanceof Folder;
	}
}
