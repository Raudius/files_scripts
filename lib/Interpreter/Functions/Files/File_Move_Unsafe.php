<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use Throwable;

/**
 * `file_move_unsafe(Node file, [String folder = nil], [String new_name = nil]): Node|null`
 *
 * Unsafe version of [`file_move`](#file_move).
 * This function expects an absolute path from the server root (not from the users home folder). This means that files can be copied to locations which the user running the action does not have access to.
 * This function performs no validation on the given path and does not check for file overwrites (overwrite handling is left up to the Nextcloud server implementation).
 *
 * ⚠️ Use of this function is strongly discouraged as it offers no safeguards against data-loss and carries potential security concerns.
 *
 * ```lua
 * local file = get_input_files()[1]
 * file_move_unsafe(file, "alice/files/inbox", "message.txt")
 * ```
 */
class File_Move_Unsafe extends RegistrableFunction {
	public function run(
		$file = null,
		$folderPath = null,
		$newName = null
	): ?array {
		$fileNode = $this->getNode($this->getPath($file));
		if (!$fileNode) {
			return null;
		}

		$newName = $newName ?: $fileNode->getName();
		$destination = $folderPath . '/' . $newName;

		try {
			$newFileNode = $fileNode->move($destination);
			return $this->getNodeData($newFileNode);
		} catch (Throwable $e) {
			return null;
		}
	}
}
