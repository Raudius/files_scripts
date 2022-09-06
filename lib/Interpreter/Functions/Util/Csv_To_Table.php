<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\NotPermittedException;
use OCP\Lock\LockedException;

/**
 * `csv_to_table(Node input, String separator=',', String enclosure='"'): Table`
 *
 * Creates a table from a CSV-formatted file.
 * Optionally field separator and enclosure characters may be specified.
 */
class Csv_To_Table extends RegistrableFunction {
	/**
	 * @param array $input
	 * @param string $separator
	 * @param string $enclosure
	 * @return array
	 */
	public function run(array $input = [], string $separator = ',', string $enclosure = '"'): array {
		try {
			$fileNode = $this->getFile($this->getPath($input));
			$fileStream = $fileNode ? $fileNode->fopen('rb') : null;
		} catch (NotPermittedException|LockedException $e) {
			return [];
		}

		if (!$fileStream) {
			return [];
		}

		$csvTable = [];
		while ($line = fgets($fileStream)) {
			$csvTable[] = $this->reindex(str_getcsv($line, $separator, $enclosure));
		}
		fclose($fileStream);
		return $this->reindex($csvTable);
	}
}
