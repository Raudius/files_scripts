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
class Sort extends RegistrableFunction {
	public function run($items = [], $key = null, $asc = true): array {
		$items = array_values($items);
		usort(
			$items,
			static function ($item1, $item2) use ($key, $asc): int {
				$e1 = ($key === null) ? $item1 : $item1[$key];
				$e2 = ($key === null) ? $item2 : $item2[$key];

				return $asc ? $e1 <=> $e2 : $e2 <=> $e1;
			}
		);

		return $this->reindex($items);
	}
}
