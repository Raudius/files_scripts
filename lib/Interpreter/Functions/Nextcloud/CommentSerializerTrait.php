<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Comments\IComment;
use OCP\Comments\ICommentsManager;

/**
 * Trait which manages Comment (de)serialization..
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

	private function deserializeComment(array $commentData, ICommentsManager $commentsManager): ?IComment {
		if (!is_array($commentData) || ($commentData["_type"] ?? null) !== "comment") {
			return null;
		}

		try {
			return $commentsManager->get($commentData['id'] ?? -1);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
