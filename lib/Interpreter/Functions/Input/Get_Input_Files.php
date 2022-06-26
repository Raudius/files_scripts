<?php

namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Node;

/**
 * `get_input_files(): Node[]`
 *
 * Returns a list of the selected files: these are the files the user selects before running the action.
 */
class Get_Input_Files extends RegistrableFunction {
	public function run(): array {
		return array_map(
			function ($file): array {
				return $this->getNodeData($file);
			},
			$this->getContext()->getInputFiles()
		);
	}
}
