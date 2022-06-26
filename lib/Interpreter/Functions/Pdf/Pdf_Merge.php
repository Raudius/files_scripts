<?php

namespace OCA\FilesScripts\Interpreter\Functions\Pdf;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\NotPermittedException;
use OCP\Lock\LockedException;
use raudius\phpdf\Operations\Merge;
use raudius\phpdf\Phpdf;
use raudius\phpdf\PhpdfException;

/**
 * `pdf_merge(Node[] files, Node folder, [String new_file_name]=nil): Node|nil`
 *
 * Merges any PDF documents in the given `files` array. The output file is saved to the specified folder.
 * The output's file name can be specified, if not specified the name `{timestamp}_merged.pdf` is used.
 *
 * The output file's node is returned, or `nil` if operation failed.
 */
class Pdf_Merge extends RegistrableFunction {
	use CheckDependency;

	public function run($files = [], $folder = [], $fileName = null): ?array {
		$this->checkDependency();
		$fileName = $fileName ?? (time() . '_merged.pdf');

		$targetFolder = $this->getFolder($this->getPath($folder));
		if (!$targetFolder || $targetFolder->nodeExists($fileName)) {
			return null;
		}

		try {
			$pdfs = [];
			foreach ($files as $file) {
				$fileNode = $this->getFile($this->getPath($file));
				if (!$fileNode) {
					continue;
				}
				$pdfs[] = Phpdf::fopen($fileNode->fopen('rb'));
			}

			$mergedPdf = (new Merge($pdfs))->execute();

			$file = $targetFolder->newFile($fileName, file_get_contents($mergedPdf->getPath()));
			return $this->getNodeData($file);
		} catch (NotPermittedException|PhpdfException|LockedException $e) {
			return null;
		}
	}
}
