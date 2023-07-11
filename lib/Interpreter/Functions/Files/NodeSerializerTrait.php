<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OC\Files\Filesystem;
use OCP\Files\Folder;
use OCP\Files\InvalidPathException;
use OCP\Files\Node;
use OCP\Files\NotFoundException;

/**
 * Trait which manages Node (de)serialization.
 * Nodes (de)serialization relies on locations relative to a home folder.
 */
trait NodeSerializerTrait {
	private function serializeNode(Node $node, Folder $homeFolder): array {
		try {
			$id = $node->getId();
		} catch (InvalidPathException|NotFoundException $e) {
			$id = null;
		}

		$root = $homeFolder;
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

	private function deserializeNode(array $nodeData, Folder $homeFolder): ?Node {
		$type = $nodeData['_type'] ?? '';
		if ($type !== "file" || !isset($nodeData['path'], $nodeData['name'])) {
			return null;
		}

		$path = ($data['path'] ?? '<no-path>') . '/' . ($data['name'] ?? '<no-name>');
		return $this->deserializeNodeFromPath($path, $homeFolder);
	}

	/**
	 * @deprecated
	 */
	private function deserializeNodeFromPath(string $nodePath, Folder $homeFolder): ?Node {
		try {
			return $homeFolder->get($nodePath);
		} catch (NotFoundException $e) {
			return null;
		}
	}
}
