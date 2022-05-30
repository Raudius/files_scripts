<?php
namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Operations\Merge;
use raudius\phpdf\Phpdf;

/**
 *
 */
class Pdf_Merge extends RegistrableFunction {
	public function getCallback(?array $files=[], string $fileName=null) {
		if (!$fileName) {
			return false; //FIXME error handling
		}

		$pdfs = [];
		foreach ($files as $file) {
			$fileNode = $this->getFile($this->getPath($file));
			if (!$fileNode) {
				return false; //FIXME error handling
			}
			$pdfs[] = Phpdf::fopen($fileNode->fopen('rb'));
		}

		$mergedPdf = (new Merge($pdfs))->execute();
		$this->getRootFolder()->newFile($fileName, file_get_contents($mergedPdf->getPath()));
		return true;
	}
}
