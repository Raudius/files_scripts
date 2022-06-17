<?php

namespace OCA\FilesScripts\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;

/**
 * @method Script findBy(string $column, string $value)
 * @method Script[] findAllBy(string $column, string $value)
 * @method Script find(int $id)
 * @method Script[] findAll()
 */
class ScriptMapper extends BaseMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'filescripts', Script::class);
	}

	public function findByTitle(string $title): ?Script {
		$title = trim($title);
		try {
			return $this->findBy('title', $title);
		} catch (MultipleObjectsReturnedException $e) {
			return $this->findAllBy('title', $title)[0] ?? null;
		}
	}
}
