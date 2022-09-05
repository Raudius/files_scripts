<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\IRootFolder;

/**
 * `exists_unsafe(String path): Bool`
 *
 * Returns whether a file or directory exists. The expected path must be from the server root directory (e.g. `/alice/files/example.txt`).
 * For most cases it is recommended to use the function [exists()](#exists).
 */
class Exists_Unsafe extends RegistrableFunction {
	private IRootFolder $root;

	public function __construct(IRootFolder $root) {
		$this->root = $root;
	}

	public function run($path = null): bool {
		if (!$path) {
			return false;
		}

		return $this->root->nodeExists($path);
	}
}
