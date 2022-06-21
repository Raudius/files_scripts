<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `copy_file(Node file, String folder_path, [String name]=nil): Bool`
 *
 * Copies the given `file` to the specified `folder_path`.
 * Optionally a new name can be specified for the file, if none is specified the original name is used.
 *
 * If the target file already exists, the operation will not succeed.
 *
 * Returns whether the operation was successful.
 */
class Copy_File extends RegistrableFunction {
	public function run(
		$file=null,
		$folderPath=null,
		$name=null
	): bool {
		$fileNode = $this->getFile($this->getPath($file));
		$folderNode = $this->getFolder($folderPath);

		if (!$fileNode) {
			return false;
		}
		if (!$folderNode || $folderNode->nodeExists($name)) {
			return false;
		}

		$name = $name ?: $fileNode->getName();
		$path = $folderNode->getPath() . '/' . $name;
		try {
			$fileNode->copy($path);
		} catch (\Exception $exception) {
			return false;
		}
		return true;
	}

}
