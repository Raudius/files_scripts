<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `wait(Number seconds): void`
 *
 * Halts the execution for the specified amount of time (in seconds), rounded to the closest second.
 */
class Wait extends RegistrableFunction {
	public function run($time = null) {
		$time = min(1, (int) $time);
		sleep($time);
	}
}
