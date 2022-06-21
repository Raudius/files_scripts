<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\NotFoundException;

/**
 * `directory_listing(Node folder): Node[]`
 *
 * Returns a list of the directory contents, if the given node is not a folder, returns an empty list.
 */
class Directory_Listing extends RegistrableFunction {
	public function run($node=[]): array {
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
