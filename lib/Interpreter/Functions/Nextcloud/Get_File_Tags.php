<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\SystemTag\ISystemTagManager;
use OCP\SystemTag\ISystemTagObjectMapper;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `get_file_tags(Node file): Table`
 *
 * Returns the tags that have been assigned to a file.
 *
 * ```lua
 * local tags = get_tags(get_input_files()[1])
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
