<?php

namespace OCA\FilesScripts\Interpreter;

use OCP\Files\Folder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;

class Context {
	/** @var Node[] */
	private array $files;
	private array $input;
	private Folder $root;
	private ?string $targetDirectory;

	public function __construct(
		Folder $root,
		array $input,
		array $files,
		?string $targetDirectory = null
	) {
		$this->root = $root;
		$this->input = $input;
		$this->files = $files;
		$this->targetDirectory = $targetDirectory;
	}

	public function getTargetDirectory(): ?Folder {
		if (!$this->targetDirectory) {
			return null;
		}

		try {
			$folder = $this->root->get($this->targetDirectory);
			if ($folder instanceof Folder) {
				return $folder;
			}
		} catch (NotFoundException $e) {
		}
		return null;
	}

	public function getRoot(): Folder {
		return $this->root;
	}

	public function getInput(): array {
		return $this->input;
	}

	/**
	 * @return Node[]
	 */
	public function getInputFiles(): array {
		return $this->files;
	}
}
