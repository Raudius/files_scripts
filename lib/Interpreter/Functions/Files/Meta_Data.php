<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use OC\Log\File;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\Folder;
use Psr\Log\LoggerInterface;

/**
 * `meta_data(Node node): Node`
 *
 * Returns an inflated Node object with additional meta-data information for the given file or directory. The additional meta-data attributes are:
 *  - `size`: the size of the file (in bytes)
 *  - `mimetype`: the mime-type of the file,
 *  - `etag`: the entity tag of the file.
 *  - `utime`: the UNIX-timestamp at which the file was uploaded to the server
 *  - `mtime`: the UNIX-timestamp at which the file was last modified
 *  - `can_read`: whether the user can read the file or can read files from the directory
 *  - `can_delete`: whether the user can delete the file or can delete files from the directory
 *  - `can_update`: whether the user can modify the file or can write to the directory
 *  - `storage_path`: the path of the file relative to its storage root
 *  - `local_path`: a path to a version of the file in the server's filesystem. This location might be temporary (local cache), if the file is stored in an external storage
 *  - `owner_id`: the user ID from the owner of the file
 */
class Meta_Data extends RegistrableFunction {
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public function run($node = null): array {
		$node = $this->getNode($this->getPath($node));
		if (!$node) {
			return [];
		}

		$type = 'unknown';
		if ($node instanceof File) {
			$type = 'file';
		} elseif ($node instanceof Folder) {
			$type = 'dir';
		}

		try {
			$sys_path = $node->getStorage()->getLocalFile($node->getInternalPath());
		} catch (\Exception|\Throwable $e) {
			$sys_path = null;

			$this->logger->warning('File action error: meta_data could not be retrieve local_path', [
				"exception" => $e->getMessage(),
				"trace" => $e->getTraceAsString()
			]);
		}

		$node_data = $this->getNodeData($node);
		try {
			$node_data = array_merge(
				$node_data,
				[
					'size' => $node->getSize(),
					'mimetype' => $node->getMimetype(),
					'etag' => $node->getEtag(),
					'mtime' => $node->getMTime(),
					'utime' => $node->getUploadTime(),
					'can_read' => $node->isReadable(),
					'can_delete' => $node->isDeletable(),
					'can_update' => $node->isUpdateable(),
					'type' => $type,
					'storage_path' => $node->getPath(),
					'local_path' => $sys_path,
					'owner_id' => $node->getOwner()->getUID(),
				],
			);
		} catch (\Exception|\Throwable $e) {
			$this->logger->error('File action error: meta_data could not be retrieved due to exception', [
				"exception" => $e->getMessage(),
				"trace" => $e->getTraceAsString()
			]);
		}

		return $node_data;
	}
}
