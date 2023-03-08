<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\SystemTag\ISystemTagManager;
use OCP\SystemTag\ISystemTagObjectMapper;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `get_file_tags(Node file): Tag[]`
 *
 * Returns a table of tags that have been assigned to a file.
 *
 * ```lua
 * -- Get tags for a file
 * local file = get_input_files()[1]
 * local tags = get_file_tags(file)
 *
 * -- Put the names of the tags into a table
 * local tag_names = {}
 * for _, tag in ipairs(tags) do
 * 	tag_names[tag.id] = tag.name
 * end
 * ```
 */
class Get_File_Tags extends RegistrableFunction {
	use TagsSerializerTrait;

	private ISystemTagManager $tagManager;
	private ISystemTagObjectMapper $tagMapper;

	public function __construct(ISystemTagManager $tagManager, ISystemTagObjectMapper $tagMapper) {
		$this->tagManager = $tagManager;
		$this->tagMapper = $tagMapper;
	}

	public function run($file=[]): array {
		$fileNode = $this->getNode($this->getPath($file));

		if (!$fileNode ) {
			return [];
		}

		$mapping = $this->tagMapper->getTagIdsForObjects($fileNode->getId(), 'files');
		if (sizeof($mapping) == 0) {
			return [];
		}

		$tags = $this->tagManager->getTagsByIds($mapping[$fileNode->getId()]);
		$tags = array_values($tags);
		return $this->reindex(array_map([$this, 'serializeTag'], $tags));
	}
}
