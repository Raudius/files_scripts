<?php

namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Phpdf;
use function raudius\phpdf\getPageCount;

/**
 * `pdf_page_count(Node node): Int`
 *
 * Returns the number of pages in the PDF document.
 * If the document is not a valid PDF document, -1 is returned.
 */
class Pdf_Page_Count extends RegistrableFunction {
	use CheckDependency;

	public function run($targetFile = []): int {
		$this->checkDependency();
		$targetFileNode = $this->getFile($this->getPath($targetFile));
		if (!$targetFileNode) {
			return -1;
		}

		$phpdf = Phpdf::fopen($targetFileNode->fopen('r'));
		return getPageCount($phpdf);
	}
}
