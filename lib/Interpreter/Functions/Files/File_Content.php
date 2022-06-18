<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\NotPermittedException;
use OCP\Lock\LockedException;

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
class File_Content extends RegistrableFunction {
	public function run($node=null): ?string {
		$node = $this->getFile($this->getPath($node));
		if (!$node) {
			return null;
		}

		try {
			return $node->getContent();
		} catch (LockedException|NotPermittedException $e) {
			return null;
		}
	}
}
