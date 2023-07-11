<?php

namespace OCA\FilesScripts\Interpreter;

use JsonSerializable;
use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Interpreter\Lua\LuaWrapper;
use OCP\Files\Folder;
use OCP\Files\Node;

class Context implements JsonSerializable {
	/** @var Node[] */
	private array $files;
	/** @var ScriptInput[] */
	private array $input;
	private Folder $root;
	private LuaWrapper $lua;

	/** @var Node[] */
	private array $viewFiles;
	/** @var string[] */
	private array $messages;
	private ?int $permissionOverride;

	public function __construct(
		LuaWrapper $lua,
		Folder $root,
		array $input,
		array $files = []
	) {
		$this->lua = $lua;
		$this->root = $root;
		$this->input = $input;
		$this->files = $files;

		$this->permissionOverride = null;
		$this->messages = [];
		$this->viewFiles = [];
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

	/**
	 * @param Node[] $files
	 */
	public function setViewFiles(array $files): void {
		$this->viewFiles = $files;
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

	public function getPermissionsOverride(): ?int {
		return $this->permissionOverride;
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


	private function getViewFileInfos(): array {
		$fileInfos = [];
		foreach ($this->viewFiles as $file) {
			try {
				$fileInfos[] = [
					'basename' => $file->getName(),
					'etag' => $file->getEtag(),
					'fileid' => $file->getId(),
					'filename' => $this->root->getRelativePath($file->getPath()),
					'mime' => $file->getMimetype(),
					'size' => $file->getSize(),
					'type' => $file->getType()
				];
			} catch (\Throwable $e) {
				continue;
			}
		}

		return $fileInfos;
	}

	public function setPermissionsOverride(int $permissions): void {
		$this->permissionOverride = $permissions;
	}

	public function jsonSerialize(): array {
		return [
			'messages' => $this->getMessages(),
			'view_files' => $this->getViewFileInfos()
		];
	}
}
