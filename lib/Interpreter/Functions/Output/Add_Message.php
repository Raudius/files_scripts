<?php

namespace OCA\FilesScripts\Interpreter\Functions\Output;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `add_message(String message, [String type="info"]): void`
 *
 * Adds a message to be shown to the user after the action is completed as a toast message. The optional type parameter
 * determines the type of toast shown.
 *
 * Type can be one of: "error", "warning", "success" or "info" (default).
 *
 * ```lua
 * add_message("I'm Blue")
 * add_message("I'm Red", "error")
 * add_message("I'm Orange", "warning")
 * add_message("I'm Green", "success")
 * ```
 */
class Add_Message extends RegistrableFunction {
	public function run($message = null, $type = null): void {
		if (!is_string($message)) {
			return;
		}
		$type = is_string($type) ? $type : "info";

		$this->getContext()->addMessage($message, $type);
	}
}
