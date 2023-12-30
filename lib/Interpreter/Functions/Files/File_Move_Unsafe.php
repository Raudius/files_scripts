<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OCP\Files\Node;

/**
 * `file_move_unsafe(Node file, [String folder = nil], [String new_name = nil]): Node|null`
 *
 * Unsafe version of [`file_move`](#file_move).
 * This function expects an absolute path from the server root (not from the user's home folder). This means that files can be copied to locations which the user running the action does not have access to.
 * This function performs no validation on the given path and does not check for file overwrites (overwrite handling is left up to the Nextcloud server implementation).
 *
 * ⚠️ Use of this function is strongly discouraged as it offers no safeguards against data loss and carries potential security concerns.
 *
 * ```lua
 * local file = get_input_files()[1]
 * file_move_unsafe(file, "alice/files/inbox", "message.txt")
 * ```
 */
class File_Move_Unsafe extends File_Move {
	use UnsafeViewOperationTrait;

	public function run(
		$file = null,
		$folderPath = null,
		$newName = null
	): ?array {
		$fileNode = $this->getNode($this->getPath($file));
		if (!$fileNode) {
			return null;
		}

		$newFile = $this->tryUnsafeOperation(function () use ($fileNode, $folderPath, $newName): Node {
			return $this->fileMove($fileNode, $folderPath, $newName);
		});

		if ($newFile instanceof Node) {
			return $this->getNodeData($newFile);
		}

		return null;
	}

	/**
	 * Hack alert!
	 * This hack was introduced, following nextcloud/server commit 55d943fd4b35c9df3e3639d6f264e4f8d8df82f0
	 *
	 * In this commit the defaultInstance is initialised to the user's home path instead of the server root.
	 *
	 * In order to trick Nextcloud into moving files to another user's home directory we need to set the defaultInstance
	 * of the Filesystem to the root view (path: `/`). This will be used during the `node->move()` method to evaluate the
	 * relative paths of the two files.
	 *
	 * @see \OC\Files\View::rename
	 * @see \OC\Files\View::getHookPath
	 */
	private function tryMoveWithDefaultInstanceOverride(Node $fileNode, string $destinationPath): ?Node {
		$defaultInstance = null;

		try {
			$class = \OC\Files\Filesystem::class;
			$reflection = new ReflectionClass($class);

			$property = $reflection->getProperty("defaultInstance");
			$property->setAccessible(true);

			$defaultInstance = $reflection->getStaticPropertyValue("defaultInstance");
			$reflection->setStaticPropertyValue('defaultInstance', new \OC\Files\View('/'));

			return $fileNode->move($destinationPath);
		} catch (\Exception|\Throwable $e) {
			return null;
		} finally {
			// Set the defaultInstance back to the previous value.
			isset($reflection) && $reflection->setStaticPropertyValue('defaultInstance', $defaultInstance);
		}
	}
}
