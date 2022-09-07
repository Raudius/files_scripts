<?php
namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * `tag_file(Node file, Tag tag): Bool`
 *
 * Adds a tag to a file. Returns whether the tag was added successfully.
 *
 * ```lua
 * local tags = tags_find({id= 42})
 * if (#tags == 1) then
 *   tag_file(get_input_files()[1], tags[1])
 * end
 * ```
 */
class Tag_File extends RegistrableFunction {
	use TagsCommand;

	public function run($file = [], $tagData = []): bool {
		$fileNode = $this->getFile($this->getPath($file));
		$tag = $this->deserializeTag($tagData);
		if (!$fileNode || !$tag) {
			return false;
		}

		try {
			$this->tagMapper->assignTags($fileNode->getId(), 'files', [$tag->getId()]);
		} catch (InvalidPathException|NotFoundException $e) {
			return false;
		}

		return true;
	}
}
