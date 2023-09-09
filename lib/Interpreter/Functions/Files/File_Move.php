<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\IRootFolder;
use OCP\Files\Node;

/**
 * `file_move(Node file, [String folder = nil], [String new_name = nil]): Node|null`
 *
 * Moves the given `file` to the specified `folder`.
 * If no folder is given, the current folder is used (file rename).
 * If no new_name is given, the old name is used.
 *
 * If the target file already exists, the operation will not succeed.
 *
 * Returns the resulting file, or `nil` if the operation failed.
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
		$fileNode = $this->getNode($this->getPath($file));
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
			$destination = $this->rootFolder->getRelativePath($folderNode->getPath());
			$newFile = $this->fileMove($fileNode, $destination, $newName);

			return $this->getNodeData($newFile);
		} catch (\Exception $e) {
			return null;
		}
	}

	protected function fileMove(Node $fileNode, string $destinationFolder, $newName): ?Node {
		$newName = (is_string($newName) && strlen($newName) > 0) ? $newName : $fileNode->getName();
		$destinationPath = $destinationFolder . '/' . $newName;
		try {
			$newFileNode = $fileNode->move($destinationPath);
			return ($newFileNode);
		} catch (\Throwable|\Exception $e) {
			return null;
		}
	}
}
