<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\SystemTag\ISystemTagManager;
use OCP\SystemTag\ISystemTagObjectMapper;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * `tag_file_unassign(Node file, Tag tag): Bool`
 *
 * Removes a tag from a file or folder. Returns whether the tag was successfully removed.
 */
class Tag_File_Unassign extends RegistrableFunction {
	use TagsSerializerTrait;

	private ISystemTagManager $tagManager;
	private ISystemTagObjectMapper $tagMapper;

	public function __construct(ISystemTagManager $tagManager, ISystemTagObjectMapper $tagMapper) {
		$this->tagManager = $tagManager;
		$this->tagMapper = $tagMapper;
	}

	public function run($file = [], $tagData = []): bool {
		$fileNode = $this->getNode($this->getPath($file));
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
