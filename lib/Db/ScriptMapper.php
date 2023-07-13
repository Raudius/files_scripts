<?php

namespace OCA\FilesScripts\Db;

use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\DB\Exception;
use OCP\IDBConnection;

/**
 * @method Script findBy(string $column, string $value)
 * @method Script[] findAllBy(string $column, string $value)
 * @method Script find(int $id)
 * @method Script[] findAll()
 */
class ScriptMapper extends BaseMapper {
	private ScriptInputMapper $scriptInputMapper;

	public function __construct(IDBConnection $db, ScriptInputMapper $scriptInputMapper) {
		parent::__construct($db, 'filescripts', Script::class);
		$this->scriptInputMapper = $scriptInputMapper;
	}

	public function findByTitle(string $title): ?Script {
		$title = trim($title);
		try {
			return $this->findBy('title', $title);
		} catch (MultipleObjectsReturnedException $e) {
			return $this->findAllBy('title', $title)[0] ?? null;
		}
	}

	/**
	 * @return Script[]
	 */
	public function findAllStripProgram(): array {
		return array_map(
			static function (Script $script): Script {
				$script->setProgram("");
				return $script;
			}, $this->findAll()
		);
	}

	/**
	 * @param Script $entity
	 * @throws Exception
	 */
	public function delete($entity): Entity {
		$this->scriptInputMapper->deleteByScriptId($entity->getId());
		return parent::delete($entity);
	}
}
