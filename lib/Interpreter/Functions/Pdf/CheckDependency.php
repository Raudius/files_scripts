<?php

namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\AbortException;
use function raudius\phpdf\checkQpdfDependency;

/**
 * Trait which throws an error if the QPDF dependency is not met.
 */
trait CheckDependency {
	/**
	 * @throws AbortException
	 */
	private function checkDependency(): void {
		if (!checkQpdfDependency()) {
			throw new AbortException('PDF operations are not possible, please contact administrator.');
		}
	}
}
