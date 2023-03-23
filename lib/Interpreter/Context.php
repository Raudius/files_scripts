<?php

namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Interpreter\Lua\LuaWrapper;
use OCP\Files\Folder;
use OCP\Files\Node;

class Context {
	/** @var Node[] */
	private array $files;
	/** @var ScriptInput[] */
	private array $input;
	private Folder $root;
	private LuaWrapper $lua;

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

	public function setPermissionsOverride(int $permissions): void {
		$this->permissionOverride = $permissions;
	}
}
