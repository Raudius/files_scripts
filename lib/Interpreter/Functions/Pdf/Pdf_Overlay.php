<?php
namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Operations\Merge;
use raudius\phpdf\Operations\Overlay;
use raudius\phpdf\Phpdf;

/**
 *
 */
class Pdf_Overlay extends RegistrableFunction {
	public function getCallback(array $targetFile=[], array $overlayFile=[], string $fileName=null, bool $repeat=true) {
		if (!$fileName) {
			return false; //FIXME error handling
		}

		$targetFileNode = $this->getFile($this->getPath($targetFile));
		$overlayFileNode = $this->getFile($this->getPath($overlayFile));
		if (!$targetFileNode || !$overlayFileNode) {
			return false; // FIXME error handling
		}

		$targetPdf = Phpdf::fopen($targetFileNode->fopen('rb'));
		$overlayPdf = Phpdf::fopen($overlayFileNode->fopen('rb'));

		(new Overlay($overlayPdf, $repeat))->execute($targetPdf);
		$this->getRootFolder()->newFile($fileName, file_get_contents($targetPdf->getPath()));
		return true;
	}
}
