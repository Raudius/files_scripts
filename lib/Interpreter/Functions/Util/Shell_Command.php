<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use mikehaertl\shellcommand\Command;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `shell_command(String command): void`
 *
 * Issues the given command to the linux shell. Returns a table with the result, the table contains the following indices:
 *   - `exit_code`
 *   - `output`
 *   - `errors`
 */
class Shell_Command extends RegistrableFunction {
	public function run($commandInput = ""): array {
		$cmd = new Command();
		$cmd->setCommand($commandInput);
		$cmd->execute();

		return [
			'exit_code' => $cmd->getExitCode(),
			'output' => $cmd->getOutput(),
			'errors' => $cmd->getStdErr()
		];
	}
}
