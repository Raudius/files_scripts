<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `file_copy_unsafe(Node file, String folder_path, [String name]=nil): Node|nil`
 *
 * Unsafe version of [`file_copy`](#file_copy).
 * This function expects an absolute path from the server root (not from the users home folder). This means that files can be copied to locations which the user running the action does not have access to.
 * This function performs no validation on the given path and does not check for file overwrites (overwrite handling is left up to the Nextcloud server implementation).
 *
 * ⚠️ Use of this function is strongly discouraged as it offers no safeguards against data-loss and carries potential security concerns.
 *
 * ```lua
 * local file = get_input_files()[1]
 * file_copy_unsafe(file, "alice/files/inbox", "message.txt")
 * ```
 */
class File_Copy_Unsafe extends RegistrableFunction {
	public function run(
		$file = null,
		$folderPath = null,
		$name = null
	) {
		$fileNode = $this->getNode($this->getPath($file));
		if (!$fileNode) {
			return null;
		}

		$name = $name ?: $fileNode->getName();
		$path = $folderPath . '/' . $name;

		try {
			$newFile = $fileNode->copy($path);
			return $this->getNodeData($newFile);
		} catch (\Exception $exception) {
			return null;
		}
	}
}
