<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\File;

/**
 *
 */
class Is_File extends RegistrableFunction {
	public function run(?array $node=[]): bool {
		return $this->getNode($this->getPath($node)) instanceof File;
	}
}
