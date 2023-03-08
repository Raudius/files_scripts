<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Comments\ICommentsManager;

/**
 * `comment_delete(Comment comment): Bool`
 *
 * Deletes a comment, returns whether the operation was successful.
 */
class Comment_Delete extends RegistrableFunction {
	use CommentSerializerTrait;

	private ICommentsManager $commentsManager;

	public function __construct(ICommentsManager $commentsManager) {
		$this->commentsManager = $commentsManager;
	}

	public function run($commentData = []): bool {
		$comment = $this->deserializeComment($commentData, $this->commentsManager);

		return $comment && $this->commentsManager->delete($comment->getId());
	}
}
