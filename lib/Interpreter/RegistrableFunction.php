<?php

namespace  OCA\FilesScripts\Interpreter;

use DateTime;
use OC\Files\Filesystem;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\InvalidPathException;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use ReflectionClass;

abstract class RegistrableFunction {
	private ?Context $context;

	public static function getFunctionName(): string {
		$name = strtolower((new ReflectionClass(static::class))->getShortName());
		return trim($name, "_");
	}

	final public function register(Context $context): void {
		$context->getLua()->registerCallback(static::getFunctionName(), function (...$args) {
			return $this->run(...$args);
		});
		$this->context = $context;
	}

	final protected function getContext(): Context {
		if (!$this->context) {
			throw new AbortException('Function has no context.');
		}

		return $this->context;
	}

	final protected function getHomeFolder(): Folder {
		$folder = $this->getContext()->getRoot();
		$this->overridePermissions($folder);
		return $folder;
	}

	final protected function getPath(array $data): string {
		return ($data['path'] ?? '<no-path>') . '/' . ($data['name'] ?? '<no-name>');
	}

	final protected function getNode(string $path): ?Node {
		if (!Filesystem::isValidPath($path)) {
			return null;
		}

		try {
			$node = $this->getHomeFolder()->get($path);
			$this->overridePermissions($node);
			return $node;
		} catch (NotFoundException $e) {
			return null;
		}
	}

	final protected function getFile(string $path): ?File {
		$node = $this->getNode($path);
		if ($node instanceof File) {
			$this->overridePermissions($node);
			return $node;
		}
		return null;
	}

	final protected function getFolder(string $path): ?Folder {
		$node = $this->getNode($path);
		if ($node instanceof Folder) {
			$this->overridePermissions($node);
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

		$root = $this->getHomeFolder();
		$path = '';
		$name = '/';
		if ($id !== $root->getId()) {
			$path = $root->getRelativePath($node->getParent()->getPath());
			$name = $node->getName();
		}

		return [
			'_type' => 'file',
			'id' => $id,
			'path' => $path,
			'name' => $name,
		];
	}

	/**
	 * Normalises the array from Lua 1-indexed array to a PHP array.
	 */
	protected function normaliseArray(array $array): array {
		$result = [];

		$isSequential = true;
		$expectedIdx = 1;

		foreach ($array as $idx => $item) {
			if (is_array($item)) {
				$item = $this->normaliseArray($item);
			}
			$result[$idx] = $item;

			if ($idx !== $expectedIdx) {
				$isSequential = false;
			}
			$expectedIdx++;
		}

		if ($isSequential) {
			return array_values($result);
		}
		return $result;
	}

	/**
	 * Will make sure the array index starts with 1.
	 *
	 * @param array $array
	 * @return array
	 */
	protected function reindex(array $array): array {
		if (isset($array[0])) {
			array_unshift($array, null);
			unset($array[0]);
		}

		foreach ($array as $key => $item) {
			if (is_array($item)) {
				$item = $this->reindex($item);
			}

			$array[$key] = $item;
		}

		return $array;
	}

	protected function packDate(DateTime $date): array {
		return [
			'year' => (int) $date->format('Y'),
			'month' => (int) $date->format('m'),
			'day' => (int) $date->format('d'),
			'hour' => (int) $date->format('H'),
			'minute' => (int) $date->format('i'),
			'second' => (int) $date->format('s')
		];
	}

	protected function unpackDate($date): DateTime {
		$date = is_array($date) ? $date : [];
		$datetime = date_create();
		if (isset($date['year'], $date['month'], $date['day'])) {
			$datetime->setDate($date['year'], $date['month'], $date['day']);
			$datetime->setTime($date['hour'] ?? 0, $date['minute'] ?? 0, $date['second'] ?? 0);
		}

		return $datetime;
	}

	/**
	 * Super hack alert!
	 * We reflect the node object to set the permissions on the fileInfo property. We assign the value from the override.
	 * This is used so that shared folders use the permissions set by the share and not the ones from the filecache folder.
	 *
	 * FIXME: If this ever gets fixed upstream maybe we can remove this.
	 */
	private function overridePermissions(Node $node): void {
		$permissions = $this->context->getPermissionsOverride();
		if ($permissions === null) {
			return;
		}

		if (!method_exists($node, 'getFileInfo')) {
			return;
		}
		$node->getFileInfo(false);

		try {
			$reflect = new \ReflectionClass($node);

			$fileInfoProp = $reflect->getProperty('fileInfo');
			$fileInfo = $fileInfoProp->getValue($node);
			$fileInfo['permissions'] = $permissions;
			$fileInfoProp->setValue($node, $fileInfo);
		} catch (\ReflectionException $e) {
		}
	}

	/**
	 * @return mixed
	 * @throws AbortException
	 */
	abstract public function run();
}
