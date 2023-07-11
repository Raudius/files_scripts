<?php

namespace OCA\FilesScripts\Interpreter\Functions\Output;

use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `abort(String message): void`
 *
 * Aborts execution with an error message. This error message will be shown to the user in a toast dialog.
 */
class Abort extends RegistrableFunction {
	public function run($error = null): void {
		$error = (string) $error;
		throw new AbortException($error);
	}
}
