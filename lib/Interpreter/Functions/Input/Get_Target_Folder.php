<?php
namespace OCA\FilesScripts\Interpreter\Functions\Input;

use Lua;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;

/**
 * Returns the target directory node.
 */
class Get_Target_Folder extends RegistrableFunction {
	private Folder $targetDirectory;

	public function __construct(Lua $lua, Folder $folder, Folder $targetDirectory) {
		parent::__construct($lua, $folder);
		$this->targetDirectory = $targetDirectory;
	}

	public function getCallback(): array {
		return $this->getNodeData($this->targetDirectory);
	}
}
