<?php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `json(Table table): String`
 *
 * Returns the json encoding of the given table. If the encoding fails, `nil` is returned.
 */
class Json extends RegistrableFunction {
	public function run(array $table = []): ?string {
		$table = $this->normaliseArray($table);
		return json_encode($table) ?: null;
	}
}
