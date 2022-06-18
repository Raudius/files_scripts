<?php
namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

class Copy_File extends RegistrableFunction {
	public function run(
		array $file=null,
		string $folderPath=null,
		string $name=null,
		bool $overwrite=false,
		bool $sequential=true
	) {
		$fileNode = $this->getFile($this->getPath($file));
		$folderNode = $this->getFolder($folderPath);

		// TODO: improve error handling
		if (!$fileNode) {
			throw new \Exception('File does not exist');
		}
		if (!$folderNode) {
			throw new \Exception('Folder does not exist');
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
