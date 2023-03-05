<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\SystemTag\ISystemTagManager;
use OCP\SystemTag\ISystemTag as Tag;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `tag_find(Table parameters): Tag[]`
 *
 * Finds existing collaborative tags. The parameters table can contain the following properties:
 * ```lua
 * local parameters = {
 *   id= 42,
 *   name= "teamA",
 *   user_visible= true,
 *   name_exact= false      -- defaults to false
 * }
 * ```
 *
 * Examples:
 * ```lua
 * tags_find()                  -- Finds all tags
 * tags({user_visible= true})   -- Finds all user-visible tags
 * tags_find({name= "2021"})    -- Finds all tags that contain the substring "2021".
 * tags_find({name= "2021", name_exact= true})   -- Finds an array containing a tag with the name "2021", or returns an empty array
 * ```
 */
class Tags_Find extends RegistrableFunction {
	use TagsSerializerTrait;

	private ISystemTagManager $tagManager;

	public function __construct(ISystemTagManager $tagManager) {
		$this->tagManager = $tagManager;
	}

	public function run($params = []): array {
		$id = $params['id'] ?? null;
		$params['name'] = $params['name'] ?? null;
		$params['user_visible'] = $params['user_visible'] ?? null;
		$params['name_exact'] = $params['name_exact'] ?? false;

		if ($id) {
			$tags = $this->findById((int) $id, $params);
		} else {
			$tags = $this->findByName($params);
		}

		$tags = array_values($tags);
		return $this->reindex(array_map([$this, 'serializeTag'], $tags));
	}

	/**
	 * @return Tag[]
	 */
	private function findById(int $id, array $params): array {
		$tags = $this->tagManager->getTagsByIds([$id]);

		return array_filter($tags, static function (Tag $tag) use ($params): bool {
			$visibilityCheck = (!$params['user_visible'] || $tag->isUserVisible() === $params['user_visible']);
			$nameCheck = true;
			if ($params['name']) {
				$nameCheck = ($params['name_exact'] ?? false)
					? $tag->getName() === $params['name']
					: strpos($tag->getName(), $params['name']) !== false;
			}

			return $nameCheck && $visibilityCheck;
		});
	}

	/**
	 * @return Tag[]
	 */
	private function findByName(array $params): array {
		$name_exact = $params['name_exact'] ?? false;
		$userVisible = $params['user_visible'] ?? true;
		$name = $params['name'] ?? '';

		if ($name_exact) {
			$tags = [$this->tagManager->getTag($name, $userVisible, true), $this->tagManager->getTag($name, $userVisible, true)];
			return array_filter($tags);
		}

		return $this->tagManager->getAllTags($userVisible, $name);
	}
}
