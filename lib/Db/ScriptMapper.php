<?php

namespace OCA\FilesScripts\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;

class ScriptMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'filescripts', Script::class);
	}

	public function find(int $id): ?Script {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('filescripts')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));

		try {
			return $this->findEntity($qb);
		} catch (DoesNotExistException|MultipleObjectsReturnedException|Exception $e) {
			return null;
		}
	}

	public function findByTitle(string $title): ?Script {
		$title = trim($title);
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('filescripts')
			->where($qb->expr()->eq('title', $qb->createNamedParameter($title)));

		try {
			$scripts = $this->findEntities($qb);
			return reset($scripts) ?: null;
		} catch (Exception $e) {
			return null;
		}
	}

	public function findAll(): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')->from('filescripts');
		return $this->findEntities($qb);
	}
}
