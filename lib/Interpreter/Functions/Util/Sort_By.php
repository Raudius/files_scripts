<?php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `sort_by(Table items, String key, [Bool ascending]=true): Table`
 *
 * Sorts a Lua table by an attribute. This is only meant for "arrays" (tables with sequential integer keys) and not for "maps" because Lua cannot guarantee sorting for associative Tables.
 *
 * Example:
 * ```
 * fruits = {{name="grape"}, {name="apple"}, {name="banana"}, {name="orange"}}
 * fruits = sort_by(fruits, "name", true)
 * -- {{name="apple"}, {name="banana"},{name="grape"},{name="orange"}}
 * ```
 */
class Sort_By extends RegistrableFunction {
	public function run(array $items = [], string $key = null, bool $asc = true): array {
		if (!$key) {
			return $items;
		}

		usort(
			$items,
			static function (array $item1, array $item2) use ($key, $asc): bool {
				if (!isset($item1[$key], $item2[$key])) {
					return -1;
				}

				return $asc
					? $item1[$key] <=> $item2[$key]
					: $item2[$key] <=> $item1[$key];
			}
		);

		return $items;
	}
}
