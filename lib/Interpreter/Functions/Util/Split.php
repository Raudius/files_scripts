<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `split(string subject, string delimiter): Table`
 *
 * Splits a string into parts based on a delimiter and returns the result as a table.
 * If the `delimiter` is not specified, it will default to splitting on whitespace.
 * If the `subject` is empty, it will return an empty table.
 *
 * Example:
 * ```lua
 * text = "apple,banana,grape"
 * fruits = split(text, ",")
 * -- {"apple", "banana", "grape"}
 * ```
 */
class Split extends RegistrableFunction {
	public function run($subject = null, $delimiter = null): array {
		if ($subject === null) {
			return [];
		}

		if ($delimiter === null) {
			$delimiter = ' ';
		}

		$parts = explode($delimiter, $subject);
		return $this->reindex($parts);
	}
}
