<?php

namespace OCA\FilesScripts\Interpreter\Functions\Input;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use Psr\Log\LoggerInterface;

/**
 * `get_target_folder(): Node|nil`
 *
 * Returns the target directory node. If none is provided, returns nil.
 *
 * ⚠️ DEPRECATED: Replace usage with user input of type "file-picker".
 * Hint: set the accepted mimetypes to just `httpd/unix-directory` to limit file picker to directories.
 */
class Get_Target_Folder extends RegistrableFunction {
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function run(): ?array {
		$this->logger->warning('[File actions]: Use of deprecated function `get_target_folder()`. Please replace usage with `get_input()` of type "file-picker".');

		$targetDirectory = $this->getContext()->getTargetDirectory();
		return $targetDirectory ? $this->getNodeData($targetDirectory) : null;
	}
}
