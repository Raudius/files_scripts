<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `for_each(Table items, Function function): Table`
 *
 * Calls the function on each key/item pair.
 * Note that inside the function only global values can be accessed.
 *
 * ```lua
 * bits = {"to", "be", "or", "not", "to", "be"}
 * sentence = ""
 *
 * for_each(
 *   bits,
 *   function (key, value)
 *     sentence = sentence .. value .. " "
 *   end
 * )
 * ```
 */
class For_Each extends RegistrableFunction {
	public function run($items = [], $function = null): void {
		if (!is_callable($function)) {
			return;
		}

		foreach ($items as $idx => $item) {
			$this->getContext()->getLua()->call($function, [$idx, $item]);
		}
	}
}
