<?php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `json(Table|string input): String|Table|nil`
 *
 * If the input is a string, returns a Table of the JSON represented in the string.
 * If the input is a table, returns the JSON representation of that object.
 * If encoding/decoding fails, `nil` is returned.
 */
class Json extends RegistrableFunction {
	public function run($input = null) {
		if (is_string($input)) {
			return json_decode($input) ?: null;
		}

		if (is_array($input)) {
			$input = $this->normaliseArray($input);
			return json_encode($input) ?: null;
		}

		return null;
	}
}
