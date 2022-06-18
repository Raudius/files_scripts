<?php
namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Operations\Decrypt;
use raudius\phpdf\Operations\Merge;
use raudius\phpdf\Operations\Overlay;
use raudius\phpdf\Phpdf;

/**
 *
 */
class Pdf_Decrypt extends RegistrableFunction {
	public function run(array $targetFile=[], string $password=null, string $newFileName=null) {
		$targetFileNode = $this->getFile($this->getPath($targetFile));
		if (!$targetFileNode) {
			return false; //FIXME error handling
		}

		$targetPdf = Phpdf::fopen($targetFileNode->fopen('rb'));

		$operation = $password ? new Decrypt($password) : new Decrypt();
		$operation->execute($targetPdf);

		$file = $targetFileNode;
		if ($newFileName) {
			$file = $this->getRootFolder()->newFile($newFileName);
		}
		$file->putContent(file_get_contents($targetPdf->getPath()));

		return true;
	}
}
