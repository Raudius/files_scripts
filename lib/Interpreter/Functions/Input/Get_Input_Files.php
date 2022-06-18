<?php
namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Node;

/**
 * Returns an array with the selected files: these are the files the user selects before running the action.
 */
class Get_Input_Files extends RegistrableFunction {
	public function run(): array {
		return array_map(
			function (Node $file): array {
				return $this->getNodeData($file);
			},
			$this->getContext()->getInputFiles()
		);
	}
}
