<?php
namespace OCA\FilesScripts\Interpreter\Functions\Input;

use Lua;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;
use OCP\Files\Node;

/**
 * Returns the array of the user input.
 */
class Get_Input extends RegistrableFunction {
	private array $input;

	/**
	 * @param Lua $lua
	 * @param Folder $folder
	 * @param array $input
	 */
	public function __construct(Lua $lua, Folder $folder, array $input) {
		parent::__construct($lua, $folder);
		$this->input = $input;
	}

	public function getCallback(): array {
		return $this->input;
	}
}
