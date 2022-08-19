<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `sort(Table items, [String key]=nil, [Bool ascending]=true): Table`
 *
 * Sorts a Lua table and returns the result.
 * If the argument `key` is returned it will sort the elements using that key (see example below).
 * If the `ascending` argument is set to `false`, the ordering will be reversed (largest first).
 *
 * This function uses [PHP's](https://www.php.net/manual/en/types.comparisons.php) default type comparison
 * This function may be slightly more convenient than Lua's own: [table.sort](https://www.lua.org/manual/5.3/manual.html#pdf-table.sort), such as in cases where you need the ascending/descending parameter.
 *
 * **Note:** if you input an associative Table, the keys will be removed in the process.
 *
 * Example:
 * ```lua
 * fruits = {"grape", "apple", "banana", "orange"}
 * fruits = sort(fruits)
 * -- {"apple", "banana", "grape", "orange"}
 *
 * fruits = {{name="grape"}, {name="apple"}, {name="banana"}, {name="orange"}}
 * fruits = sort(fruits, "name", true)
 * -- {{name="apple"}, {name="banana"},{name="grape"},{name="orange"}}
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
