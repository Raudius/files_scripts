<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use Exception;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Comments\IComment;
use OCP\Comments\ICommentsManager;
use OCP\Files\Node;

/**
 * `comments_find(Table parameters): Comment[]`
 *
 * Finds comment objects. The parameters table can contain the following properties:
 * ```lua
 * local parameters = {
 *   id= 481,                     -- Returns the comment with ID 481
 *   parent_id= 612,              -- Returns the children of comment 612
 *   node= get_input_files()[1],  -- Returns the comments for the file
 * }
 * ```
 *
 * It searches for each of the provided parameters in order: `id`, `parent_id`, `file`. Returns as the first set of results possible.
 * So if it finds a file by `id` it won't continue searching by `parent_id` or `file`.
 *
 * Examples:
 * ```lua
 * tags({file= get_input_files()[1]}) -- Finds comments for a file
 * tags({id= 21})                     -- Finds comment with ID 21
 * tags({parent_id= 13})              -- Finds comments tree of comment 13
 * tags({id= 21, parent_id= 13})      -- Finds comment with ID 21 or (if comment 21 does not exist) the comment tree of comment 13
 * ```
 */
class Comments_Find extends RegistrableFunction {
	use CommentSerializerTrait;

	private ICommentsManager $commentsManager;

	public function __construct(ICommentsManager $commentsManager) {
		$this->commentsManager = $commentsManager;
	}

	public function run($params = []): array {
		$id = $params['id'] ?? null;
		$parentId = $params['parent_id'] ?? null;
		$nodeData = $params['node'] ?? null;

		$comments = [];
		if ($id) {
			$comments = $this->findById((int) $id);
		}

		if (empty($comments) && $parentId) {
			$comments = $this->findByParentId($parentId);
		}

		if (empty($comments) && $nodeData) {
			$file = $this->getNode($this->getPath($nodeData));
			$comments = $file ? $this->findByFile($file) : null;
		}


		return $this->reindex(array_map([$this, 'serializeComment'], $comments));
	}

	/**
	 * @return IComment[]
	 */
	private function findById(int $id): array {
		try {
			return [$this->commentsManager->get($id)];
		} catch (Exception $e) {
			return [];
		}
	}

	/**
	 * @return IComment[]
	 */
	private function findByParentId(int $id): array {
		return $this->commentsManager->getTree($id)['replies'] ?? [];
	}

	/**
	 * @return IComment[]
	 */
	private function findByFile(Node $file): array {
		try {
			return $this->commentsManager->getForObject('files', (string) $file->getId());
		} catch (Exception $e) {
			return [];
		}
	}
}
