<?php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 *
 */
class Sort_By extends RegistrableFunction {
	public function run(array $items = [], string $key = '', bool $asc = true): array {
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

		// 1-index for Lua
		$n = 1;
		$sorted = [];
		foreach ($items as $item) {
			$sorted[$n++] = $item;
		}
		return $sorted;
	}
}
