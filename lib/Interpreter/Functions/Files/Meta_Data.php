<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OC\Log\File;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;

/**
 * `meta_data(Node node): Node`
 *
 * Returns an inflated Node object with additional meta-data information for the given file or directory. The additional meta-data attributes are:
 *  - `size`: the size of the file (in bytes)
 *  - `mimetype`: the mime-type of the file,
 *  - `etag`: the entity tag of the file.
 *  - `utime`: the UNIX-timestamp at which the file was uploaded to the server
 *  - `mtime`: the UNIX-timestamp at which the file was last modified
 *  - `can_read`: whether the user can read the file or can read files from the directory
 *  - `can_delete`: whether the user can delete the file or can delete files from the directory
 *  - `can_update`: whether the user can modify the file or can write to the directory
 */
class Meta_Data extends RegistrableFunction {
	public function run($node = null): array {
		$node = $this->getNode($this->getPath($node));
		if (!$node) {
			return [];
		}

		$type = 'unknown';
		if ($node instanceof File) {
			$type = 'file';
		} elseif ($node instanceof Folder) {
			$type = 'dir';
		}

		return array_merge(
			$this->getNodeData($node),
			[
				'size' => $node->getSize(),
				'mimetype' => $node->getMimetype(),
				'etag' => $node->getEtag(),
				'mtime' => $node->getMTime(),
				'utime' => $node->getUploadTime(),
				'can_read' => $node->isReadable(),
				'can_delete' => $node->isDeletable(),
				'can_update' => $node->isUpdateable(),
				'type' => $type
			],
		);
	}
}
