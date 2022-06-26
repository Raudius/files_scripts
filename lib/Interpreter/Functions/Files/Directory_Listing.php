<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\NotFoundException;

/**
 * `directory_listing(Node folder, [String filter_type]='all'): Node[]`
 *
 * Returns a list of the directory contents, if the given node is not a folder, returns an empty list.
 * Optionally a second argument can be provided to filter out files or folders:
 *  - If `"file"` is provided: only files are returned
 *  - If `"folder"` is provided: only folders are returned
 *  - If any other value is provided: both files and folders are returned.
 */
class Directory_Listing extends RegistrableFunction {
	public function run($node = [], $filterType = null): array {
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
			if ($this->filterType($item, $filterType)) {
				$nodes[$n++] = $this->getNodeData($item);
			}
		}

		return $nodes;
	}

	private function filterType($item, $filterType): bool {
		if ($filterType === 'file') {
			return $item instanceof File;
		}

		if ($filterType === 'folder') {
			return $item instanceof Folder;
		}

		return true; // unknown filter
	}
}
