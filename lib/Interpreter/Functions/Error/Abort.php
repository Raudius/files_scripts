<?php

namespace OCA\FilesScripts\Interpreter\Functions\Error;

use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `abort(String message): void`
 *
 * Aborts execution with an error message. This error message will be shown to the user in a toast dialog.
 */
class Abort extends RegistrableFunction {
	public function run($error = null): array {
		$error = (string) $error;
		throw new AbortException($error);
	}
}
