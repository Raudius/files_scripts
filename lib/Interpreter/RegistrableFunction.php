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
		return strtolower((new ReflectionClass(static::class))->getShortName());
	}

	final public function register($context): void {
		$context->getLua()->registerCallback(static::getFunctionName(), [$this, 'run']);
		$this->context = $context;
	}

	final protected function getContext(): Context {
		if (!$this->context) {
			throw new AbortException('Function has no context.');
		}

		return $this->context;
	}

	final protected function getRootFolder(): Folder {
		return $this->getContext()->getRoot();
	}

	final protected function getPath(array $data): string {
		return ($data['path'] ?? '<no-path>') . '/' . ($data['name'] ?? '<no-name>');
	}

	final protected function getNode(string $path): ?Node {
		if (!Filesystem::isValidPath($path)) {
			return null;
		}

		try {
			return $this->getRootFolder()->get($path);
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

		$root = $this->getRootFolder();
		$path = '';
		$name = '/';
		if ($id !== $root->getId()) {
			$path = $root->getRelativePath($node->getParent()->getPath());
			$name = $node->getName();
		}

		return [
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
	 * @return mixed
	 * @throws AbortException
	 */
	abstract public function run();
}
