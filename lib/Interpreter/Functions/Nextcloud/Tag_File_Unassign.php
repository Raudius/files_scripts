<?php
namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * `tag_file_unassign(Node file, Tag tag): Bool`
 *
 * Removes a tag from a file. Returns whether the tag was successfully removed.
 */
class Tag_File_Unassign extends RegistrableFunction {
	use TagsCommand;

	public function run($file = [], $tagData = []): bool {
		$fileNode = $this->getFile($this->getPath($file));
		$tag = $this->deserializeTag($tagData);
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
