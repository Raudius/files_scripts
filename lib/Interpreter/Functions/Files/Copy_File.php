<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Psr\Log\LoggerInterface;

/**
 * `copy_file(Node file, String folder_path, [String name]=nil): Bool`
 *
 * ⚠️ DEPRECATED: This function will be removed in v2.0.0. See [file_copy](#file_copy)
 */
class Copy_File extends File_Copy {

	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	/**
	 * TODO: Remove this function for v2 and add return to File_Copy function signature.
	 *
	 * @param $file
	 * @param $folderPath
	 * @param $name
	 * @return bool
	 * @throws \OCA\FilesScripts\Interpreter\AbortException
	 */
	public function run(
		$file = null,
		$folderPath = null,
		$name = null
	) {
		$this->logger->warning("[File actions]: Use of deprecated function `copy_file()`, please use `file_copy()` instead.");
		return (bool) parent::run($file, $folderPath, $name);
	}
}
