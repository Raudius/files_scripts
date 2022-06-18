<?php
namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * Returns the target directory node. If none is provided, returns nil.
 */
class Get_Target_Folder extends RegistrableFunction {

	public function run(): ?array {
		$targetDirectory = $this->getContext()->getTargetDirectory();
		return $targetDirectory ? $this->getNodeData($targetDirectory) : null;
	}
}
