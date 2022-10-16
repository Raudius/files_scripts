<?php
namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Comments\ICommentsManager;
use OCP\IUserManager;
use OCP\IUserSession;

/**
 * `comment_create(String message, Node target, Table parameters={}): ?Comment`
 *
 * Writes a comment to a file or folder, returns the resulting comment object (or nil if failed).
 *
 * The extra parameters table accepts:
 * ```lua
 * paramters = {
 *   unsafe_impersonate_user= users_find({ ... })[1]   -- Warning: This parameter breaks intended comment behaviour
 * }
 * ```
 *
 * Example:
 * ```lua
 * comment_create("Hello world!", get_input_files()[1])
 * ```
 */
class Comment_Create extends RegistrableFunction {
	use CommentSerializerTrait;
	use UserSerializerTrait;

	private ICommentsManager $commentsManager;
	private IUserManager $userManager;
	private IUserSession $userSession;

	public function __construct(ICommentsManager $commentsManager, IUserSession $userSession, IUserManager $userManager) {
		$this->commentsManager = $commentsManager;
		$this->userSession = $userSession;
		$this->userManager = $userManager;
	}

	public function run($message='', $nodeData=[], $params=[]): ?array {
		$node = $this->getNode($this->getPath($nodeData));
		$user = isset($params['unsafe_impersonate_user'])
			? $this->deserializeUser($params['unsafe_impersonate_user'], $this->userManager)
			: $this->userSession->getUser();

		if (!$user || !$node) {
			return null;
		}

		try {
			$comment = $this->commentsManager->create('users', $user->getUID(), 'files', (string) $node->getId());
			$comment->setMessage($message);
			$comment->setVerb('comment');
			$success = $this->commentsManager->save($comment);

			return $success ? $this->serializeComment($comment) : null;
		} catch (Exception $e) {
			return null;
		}
	}
}
