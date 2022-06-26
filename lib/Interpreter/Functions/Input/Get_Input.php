<?php

namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `get_input(): Table`
 *
 * Returns a Lua table containing the user inputs.
 */
class Get_Input extends RegistrableFunction {
	public function run(): array {
		return $this->getContext()->getInput();
	}
}
