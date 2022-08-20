<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\IRootFolder;

/**
 * `file_move(Node file, [String folder = nil], [String new_name = nil]): Node|null`
 *
 * Moves the given `file` to the specified `folder`.
 * If no folder is given, the current folder is used (file rename).
 * If no new_name is given, the old name is used.
 *
 * If the target file already exists, the operation will not succeed.
 *
 * Returns the resulting file, or nil if the operation failed.
 */
class File_Move extends RegistrableFunction {
	private IRootFolder $rootFolder;

	public function __construct(IRootFolder $rootFolder) {
		$this->rootFolder = $rootFolder;
	}

	public function run(
		$file = null,
		$folderPath = null,
		$newName = null
	): ?array {
		$fileNode = $this->getFile($this->getPath($file));
		if (!$fileNode) {
			return null;
		}

		$newName = $newName ?: $fileNode->getName();
		$folderNode = $folderPath ? $this->getFolder($folderPath) : null;
		$folderNode = $folderNode ?? $fileNode->getParent();

		if (!$folderNode || $folderNode->nodeExists($newName)) {
			return null;
		}

		try {
			$destination = $this->rootFolder->getRelativePath($folderNode->getPath()) . "/$newName";
			$newFileNode = $fileNode->move($destination);
			return $this->getNodeData($newFileNode);
		} catch (Exception $e) {
			return null;
		}
	}
}
