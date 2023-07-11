<?php

namespace OCA\FilesScripts\Interpreter\Functions\Output;

use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `view_files(Node[] nodes): void`
 *
 * Sets a list of files to be viewed after execution.
 */
class View_Files extends RegistrableFunction {
	public function run($nodeDatas = []): void {
		$nodes = [];
		foreach ($nodeDatas as $nodeData) {
			if (!is_array($nodeData)) {
				continue;
			}

			$path = $this->getPath($nodeData);
			$node = $this->getNode($path);
			if ($node === null) {
				continue;
			}

			$nodes[] = $node;
		}

		$this->getContext()->setViewFiles($nodes);
	}
}
