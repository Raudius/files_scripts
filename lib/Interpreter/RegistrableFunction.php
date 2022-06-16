<?php
namespace  OCA\FilesScripts\Interpreter;

use Lua;
use OC\Files\Filesystem;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\InvalidPathException;
use OCP\Files\Node;
use OCP\Files\NotFoundException;

abstract class RegistrableFunction {
	private Lua $lua;
	private Folder $folder;

	public function __construct(Lua $lua, Folder $folder) {
		$this->lua = $lua;
		$this->folder = $folder;
	}

	final public function register() {
		$name = strtolower((new \ReflectionClass($this))->getShortName());
		$this->lua->registerCallback($name, [$this, 'getCallback']);
	}

	final protected function getVariable(string $name) {
		return $this->lua->eval(<<<LUA
if $name then
	return $name
end
return nil
LUA);
	}

	final protected function getRootFolder(): Folder {
		return $this->folder;
	}

	final protected function getPath(array $data): string {
		return ($data['path'] ?? '') . '/' . ($data['name'] ?? '');
	}

	final protected function getNode(string $path): ?Node {
		if (!Filesystem::isValidPath($path)) {
			return null;
		}

		try {
			return $this->folder->get($path);
		} catch (NotFoundException $e) {
			return null;
		}
	}

	final protected function getFile(string $path): ?File {
		$node = $this->getNode($path);
		if ($node instanceof File) {
			return $node;
		}
		return null;
	}

	final protected function getFolder(string $path): ?Folder {
		$node = $this->getNode($path);
		if ($node instanceof Folder) {
			return $node;
		}
		return null;
	}

	final protected function getNodeData(Node $node): array {
		try {
			$id = $node->getId();
		} catch (InvalidPathException|NotFoundException $e) {
			$id = null;
		}

		$path = '';
		$name = '/';
		if ($id !== $this->folder->getId()) {
			$path = $this->folder->getRelativePath($node->getParent()->getPath());
			$name = $node->getName();
		}

		return [
			'id' => $id,
			'path' => $path,
			'name' => $name,
		];
	}

	/**
	 * @return mixed
	 * @throws AbortException
	 */
	abstract public function getCallback();
}
