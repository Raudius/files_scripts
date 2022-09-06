<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use Psr\Log\LoggerInterface;

/**
 * `root(): Node`
 *
 * ⚠️ DEPRECATED: See [home](#home)
 */
class Root extends RegistrableFunction {

	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function run($node = null): array {
		$this->logger->warning("[File actions]: Use of deprecated function `root()`, please use `home()` instead. In future API versions the `root()` may be repurposed and break your scripts.");
		return $this->getNodeData($this->getHomeFolder());
	}
}
