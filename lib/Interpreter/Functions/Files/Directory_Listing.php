<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\NotFoundException;

/**
 *
 */
class Directory_Listing extends RegistrableFunction {
	public function getCallback(?array $node=[]): array {
		$folder = $this->getNode($this->getPath($node));
		if (false === $folder instanceof Folder) {
			return [];
		}

		try {
			$items = $folder->getDirectoryListing();
		} catch (NotFoundException $e) {
			return [];
		}

		$nodes = [];
		$n = 1;
		foreach ($items as $item) {
			$nodes[$n++] = $this->getNodeData($item);
		}

		return $nodes;
	}
}
