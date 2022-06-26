<?php

namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\NotPermittedException;
use OCP\Lock\LockedException;
use raudius\phpdf\Operations\Trim;
use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;

/**
 * `pdf_pages(Node file, String page_range, [String new_file_name]=nil): Node|nil`
 *
 * Creates a new PDF only containing the specified pages. Page range parameter allows multiple formats see [qpdf documentation](https://qpdf.readthedocs.io/en/stable/cli.html#page-ranges).
 *
 * Returns the output file's node object, or `nil` if operation failed.
 */
class Pdf_Pages extends RegistrableFunction {
	use CheckDependency;

	public function run($file = [], $pages = '', $fileName = null): ?array {
		$this->checkDependency();
		$fileName = $fileName ?? (time() . '_trimmed.pdf');
		$fileNode = $this->getNode($this->getPath($file));

		if (!$fileNode) {
			return null;
		}

		try {
			$pagesPdf = (new Trim($pages))->execute(Phpdf::fopen($fileNode->fopen('rb')));
			$fileOut = $fileNode->getParent()->newFile($fileName, file_get_contents($pagesPdf->getPath()));
			return $this->getNodeData($fileOut);
		} catch (NotPermittedException|PhpdfException|LockedException $e) {
			return null;
		}
	}
}
