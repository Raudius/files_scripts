<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

/**
 * `file_copy(Node node, String folder_path, [String name]=nil): Node|nil`
 *
 * Copies the given node (file or folder) to the specified `folder_path`.
 * Optionally a new name can be specified for the file, if none is specified the original name is used.
 *
 * If the target file already exists, the operation will not succeed.
 *
 * Returns the resulting file node, or `nil` if the operation failed.
 */
class File_Copy extends File_Copy_Unsafe {
	public function run(
		$file = null,
		$folderPath = null,
		$name = null
	) {
		$fileNode = $this->getNode($this->getPath($file));
		$folderNode = $this->getFolder($folderPath);
		if (!$fileNode || !$folderNode) {
			return null;
		}

		$newName = $name ?: $fileNode->getName();
		if ($folderNode->nodeExists($newName)) {
			return null;
		}

		return parent::run($file, $folderNode->getPath(), $name);
	}
}
