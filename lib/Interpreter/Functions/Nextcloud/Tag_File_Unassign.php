<?php
namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OC\SystemTag\SystemTagManager;
use OC\SystemTag\SystemTagObjectMapper;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * `tag_file_unassign(Node file, Tag tag): Bool`
 *
 * Removes a tag from a file. Returns whether the tag was successfully removed.
 */
class Tag_File_Unassign extends RegistrableFunction {
	use TagsSerializerTrait;

	private SystemTagManager $tagManager;
	private SystemTagObjectMapper $tagMapper;

	public function __construct(SystemTagManager $tagManager, SystemTagObjectMapper $tagMapper) {
		$this->tagManager = $tagManager;
		$this->tagMapper = $tagMapper;
	}

	public function run($file = [], $tagData = []): bool {
		$fileNode = $this->getFile($this->getPath($file));
		$tag = $this->deserializeTag($tagData, $this->tagManager);
		if (!$fileNode || !$tag) {
			return false;
		}

		try {
			$this->tagMapper->unassignTags($fileNode->getId(), 'files', [$tag->getId()]);
		} catch (InvalidPathException|NotFoundException $e) {
			return false;
		}

		return true;
	}
}
