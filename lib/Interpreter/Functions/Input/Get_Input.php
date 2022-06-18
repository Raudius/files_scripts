<?php
namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * Returns a table containing the user inputs.
 */
class Get_Input extends RegistrableFunction {

	public function run(): array {
		return $this->getContext()->getInput();
	}
}
