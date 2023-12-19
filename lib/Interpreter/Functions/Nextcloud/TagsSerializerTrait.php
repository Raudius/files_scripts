<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\SystemTag\ISystemTag as Tag;
use OCP\SystemTag\ISystemTagManager;

/**
 * Trait which manages Tag (de)serialization..
 */
trait TagsSerializerTrait {
	private function serializeTag(Tag $tag): array {
		return [
			'_type' => 'tag',
			'id' => $tag->getId(),
			'name' => $tag->getName(),
			'user_assignable' => $tag->isUserAssignable(),
			'user_visible' => $tag->isUserVisible(),
			'access_level' => $tag->getAccessLevel()
		];
	}

	private function deserializeTag(array $tagData, ISystemTagManager $tagManager): ?Tag {
		if (!is_array($tagData) || ($tagData["_type"] ?? null) !== "tag") {
			return null;
		}

		try {
			$tags = $tagManager->getTagsByIds([$tagData['id'] ?? -1]);
		} catch (\Throwable $e) {
			return null;
		}
		return reset($tags) ?: null;
	}
}
