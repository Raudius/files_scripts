<?php

namespace OCA\FilesScripts\Interpreter\Functions\Output;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `clear_messages(String message, [String type="info"]): void`
 *
 * Clears all messages that have been previously added with [`add_message`](#add_message).
 *
 * ```lua
 * add_message("Don't show this...")
 * add_message("...or this")
 *
 * clear_messages()
 *
 * add_message("Show this")
 * add_message("...and this")
 * ```
 */
class Clear_Messages extends RegistrableFunction {
	public function run(): void {
		$this->getContext()->clearMessages();
	}
}
