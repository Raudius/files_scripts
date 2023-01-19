<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OC\SystemTag\SystemTagManager;
use OC\SystemTag\SystemTagObjectMapper;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\InvalidPathException;
use OCP\Files\NotFoundException;

/**
 * `get_tags(Node file): array`
 *
 * Gets tags of a file. Returns list of tags.
 *
 * ```lua
 * local tags = get_tags(get_input_files()[1])
 *
 * ```
 */
class Get_Tags extends RegistrableFunction {
        use TagsSerializerTrait;

        private SystemTagManager $tagManager;
        private SystemTagObjectMapper $tagMapper;

        public function __construct(SystemTagManager $tagManager, SystemTagObjectMapper $tagMapper) {
                $this->tagManager = $tagManager;
                $this->tagMapper = $tagMapper;
        }

        public function run($file = []): array {
                $fileNode = $this->getNode($this->getPath($file));

                if (!$fileNode ) {
                        return [];
                }

                $mapping = $this->tagMapper->getTagIdsForObjects($fileNode->getId(), 'files');
                if (sizeof($mapping) == 0) {
                        return [];
                }

                $tags = $this->tagManager->getTagsByIds($mapping[$fileNode->getId()]);

                return $tags;
        }
}