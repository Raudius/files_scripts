<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Comments\IComment;
use OCP\Comments\ICommentsManager;

/**
 * Trait which manages Tag (de)serialization..
 */
trait CommentSerializerTrait {
	private function serializeComment(IComment $comment): array {
		return [
			'_type' => 'comment',
			'id' => $comment->getId(),
			'parent_id' => $comment->getParentId(),
			'object_id' => $comment->getObjectId(),
			'user_id' => $comment->getActorId(),
			'created_at' => $comment->getCreationDateTime()->getTimestamp(),
			'message' => $comment->getMessage(),
		];
	}

	private function deserializeComment(array $tagData, ICommentsManager $commentsManager): ?IComment {
		try {
			return $commentsManager->get($tagData['id'] ?? -1);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
