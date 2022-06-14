<?php
namespace OCA\FilesScripts\Interpreter\Functions\Input;

use Lua;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;
use OCP\Files\Node;

/**
 * Returns the array of input files.
 */
class Get_Input_Files extends RegistrableFunction {
	/** @var Node[] */
	private array $files;

	/**
	 * @param Lua $lua
	 * @param Folder $folder
	 * @param Node[] $files
	 */
	public function __construct(Lua $lua, Folder $folder, array $files) {
		parent::__construct($lua, $folder);
		$this->files = $files;
	}

	public function getCallback(): array {
		return array_map(
			function (Node $file): array {
				return $this->getNodeData($file);
			},
			$this->files
		);
	}
}
