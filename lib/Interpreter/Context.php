<?php

namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Interpreter\Lua\LuaWrapper;
use OCP\Files\Folder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;

class Context {
	/** @var Node[] */
	private array $files;
	/** @var ScriptInput[] */
	private array $input;
	private Folder $root;
	private ?string $targetDirectory;
	private LuaWrapper $lua;

	private array $messages = [];

	public function __construct(
		LuaWrapper $lua,
		Folder $root,
		array $input,
		array $files,
		?string $targetDirectory = null
	) {
		$this->lua = $lua;
		$this->root = $root;
		$this->input = $input;
		$this->files = $files;
		$this->targetDirectory = $targetDirectory;

		$this->messages = [];
	}

	public function clearMessages(): void {
		$this->messages = [];
	}

	public function addMessage($message, $type=null): void {
		$this->messages[] = [
			'message' => $message,
			'type' => $type
		];
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


	public function getLua(): LuaWrapper {
		return $this->lua;
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

	public function getMessages(): array {
		return $this->messages;
	}
}
