<?php
namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Operations\Merge;
use raudius\phpdf\Phpdf;

/**
 *
 */
class Pdf_Merge extends RegistrableFunction {
	public function run(?array $files=[], array $folder=[], string $fileName=null) {
		if (!$fileName) {
			return false; //FIXME error handling
		}

		$targetFolder = $this->getFolder($this->getPath($folder));
		if (!$targetFolder) {
			return false;
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

		$targetFolder->newFile($fileName, file_get_contents($mergedPdf->getPath()));
		return true;
	}
}
