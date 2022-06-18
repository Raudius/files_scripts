<?php
namespace OCA\FilesScripts\Interpreter\Functions\Error;

use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * Aborts execution with an error message.
 */
class Abort extends RegistrableFunction {
	public function run($error=null): array {
		throw new AbortException($error ?? "");
	}
}
