<?php

namespace OCA\FilesScripts\Interpreter\Functions\Output;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use Psr\Log\LoggerInterface;

/**
 * `log(String message, [Int level=1], [Table context={}]): void`
 *
 * Logs a message to the Nextcloud log.
 *
 * You may optionally specify a [log level](https://docs.nextcloud.com/server/latest/admin_manual/configuration_server/logging_configuration.html#log-level) (defaults to 1).
 * You may append some context to the log by passing a table containing the relevant data.
 */
class Log extends RegistrableFunction {
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function run($message = '', $level = 1, $context = []) {
		$message = (string) $message;
		if (strlen($message) <= 0) {
			return;
		}

		$level = is_int($level) ? $level : 1;
		$level = max(min($level, 0), 4);
		$context = is_array($context) ? $context : [];

		$this->logger->log($level, $message, $context);
	}
}
