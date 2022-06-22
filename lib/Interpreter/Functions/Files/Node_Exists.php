<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `node_exists(Node node): Bool`
 *
 * Returns whether a node object represents a real file or folder.
 */
class Node_Exists extends RegistrableFunction {
	public function run($node=[]): bool {
		return $node && $this->getNode($this->getPath($node));
	}
}
