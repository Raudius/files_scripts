<?php

namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use raudius\phpdf\Operations\Overlay;
use raudius\phpdf\Phpdf;

/**
 * `pdf_overlay(Node target, Node overlay, [String new_file_name]=null, [Bool repeat]=true): Node`
 *
 * Overlays the `overlay` PDF document onto the `target` PDF file. The overlay happens sequentially: page 1 of `overlay` gets rendered over page 1 of `target`, page 2 over page 2...
 * By default, the overlay repeats (after we run out of overlay pages we start again from page 1), this can be changed by setting the `repeat` parameter to `false`.
 *
 * A new file can be created by specifying the `new_file_name` parameter (the file will be created on the target file's folder). By default, the target file gets overwritten.
 *
 * Returns the node object of the resulting file.
 */
class Pdf_Overlay extends RegistrableFunction {
	use CheckDependency;

	public function run($targetFile = [], $overlayFile = [], $newFileName = null, $repeat = true): ?array {
		$this->checkDependency();
		$targetFileNode = $this->getFile($this->getPath($targetFile));
		$overlayFileNode = $this->getFile($this->getPath($overlayFile));
		if (!$targetFileNode || !$overlayFileNode) {
			return null;
		}

		try {
			$targetPdf = Phpdf::fopen($targetFileNode->fopen('rb'));
			$overlayPdf = Phpdf::fopen($overlayFileNode->fopen('rb'));

			$file = $targetFileNode;
			if ($newFileName) {
				$file = $targetFileNode->getParent()->newFile($newFileName);
			}
			(new Overlay($overlayPdf, $repeat))->execute($targetPdf);
			$file->putContent(file_get_contents($targetPdf->getPath()));
		} catch (Exception $e) {
			return null;
		}

		return $this->getNodeData($file);
	}
}
