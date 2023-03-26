<?php

namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `get_input([String input_name=nil]): Table|any`
 *
 * Returns a Lua table containing the user inputs. If the optional `input_name` parameter is specified the value of the matching input is returned.
 *
 * ```lua
 * get_input() 			-- { testVar= 'input' }
 * get_input('testVar') -- 'input'
 * ```
 */
class Get_Input extends RegistrableFunction {
	public function run($inputName = null) {
		$inputs = [];
		foreach ($this->getContext()->getInput() as $input) {
			$inputs[$input->getName()] = $this->getRuntimeValue($input);
		}

		return $inputName === null
			? $inputs
			: $inputs[$inputName] ?? null;
	}

	private function getRuntimeValue(ScriptInput $scriptInput) {
		$scriptType = $scriptInput->getScriptOptions()['type'] ?? null;
		$value = $scriptInput->getValue();

		switch ($scriptType) {
			case 'filepick':
				$node = $this->getNode($value);
				return $value && $node ? $this->getNodeData($node) : null;
			case 'checkbox':
				return (bool) $value;
		}

		if (is_array($value)) {
			return $this->reindex($value);
		}

		return (string) $value;
	}
}
