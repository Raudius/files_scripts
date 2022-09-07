<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OC\SystemTag\SystemTagObjectMapper;
use OCP\SystemTag\ISystemTag as Tag;
use OC\SystemTag\SystemTagManager;

/**
 * Trait which manages Tag (de)serialization..
 */
trait TagsCommand {
	private SystemTagManager $tagManager;
	private SystemTagObjectMapper $tagMapper;

	public function __construct(SystemTagManager $tagManager, SystemTagObjectMapper $tagMapper) {
		$this->tagManager = $tagManager;
		$this->tagMapper = $tagMapper;
	}

	private function serializeTag(Tag $tag): array {
		return [
			'id' => $tag->getId(),
			'name' => $tag->getName(),
			'user_assignable' => $tag->isUserAssignable(),
			'user_visible' => $tag->isUserVisible(),
			'access_level' => $tag->getAccessLevel()
		];
	}

	private function deserializeTag(array $tagData): ?Tag {
		try {
			$tags = $this->tagManager->getTagsByIds([$tagData['id'] ?? -1]);
		} catch (\Throwable $e) {
			return null;
		}
		return reset($tags) ?: null;
	}
}
