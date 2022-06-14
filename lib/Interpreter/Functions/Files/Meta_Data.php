<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * meta_data(Node node)
 *
 * Returns the meta-data for a given file or directory. The meta-data contains:
 * 	- `size`: the size of the file (in bytes)
 *  - `mime_type`: the mime-type of the file
 *  - `uploaded_at`: the UNIX-timestamp at which the file was uploaded to the server
 *  - `created_at`: the UNIX-timestamp at which the file was created
 *  - `modified_at`: the UNIX-timestamp at which the file was last modified
 *  - `can_read`: whether the user can read the file or can read files from the directory
 *  - `can_delete`: whether the user can delete the file or can delete files from the directory
 *  - `can_update`: whether the user can modify the file or can write to the directory
 */
class Meta_Data extends RegistrableFunction {
	public function getCallback($node=null): array {
		$node = $this->getNode($this->getPath($node));
		if (!$node) {
			return [];
		}

		return [
			'size' => $node->getSize(),
			'mime_type' => $node->getMimetype(),
			'created_at' => $node->getCreationTime(),
			'modified_at' => $node->getMTime(),
			'uploaded_at' => $node->getUploadTime(),
			'can_read' => $node->isReadable(),
			'can_delete' => $node->isDeletable(),
			'can_update' => $node->isUpdateable(),
		];
	}
}
