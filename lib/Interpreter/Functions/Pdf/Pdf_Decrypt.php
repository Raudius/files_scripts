<?php

namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Operations\Decrypt;
use raudius\phpdf\Phpdf;

/**
 * `pdf_decrypt(Node file, [String password]=nil, [String new_file_name]=nil): Node|nil`
 *
 * Removes protections from the PDF file. If `new_file_name` is specified a new file is created, otherwise the existing file gets overwritten.
 *
 * Returns the node object for the resulting file.
 */
class Pdf_Decrypt extends RegistrableFunction {
	use CheckDependency;

	public function run($targetFile = [], $password = null, $newFileName = null): ?array {
		$this->checkDependency();
		$targetFileNode = $this->getFile($this->getPath($targetFile));
		if (!$targetFileNode) {
			return null;
		}

		try {
			$targetPdf = Phpdf::fopen($targetFileNode->fopen('rb'));

			$operation = $password ? new Decrypt($password) : new Decrypt();
			$operation->execute($targetPdf);

			$file = $targetFileNode;
			if ($newFileName) {
				$file = $targetFileNode->getParent()->newFile($newFileName);
			}
			$file->putContent(file_get_contents($targetPdf->getPath()));
		} catch (Exception $e) {
			return null;
		}

		return $this->getNodeData($file);
	}
}
