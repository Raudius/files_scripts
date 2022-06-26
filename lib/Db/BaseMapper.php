<?php

namespace OCA\FilesScripts\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;

abstract class BaseMapper extends QBMapper {
	protected function deleteBy(string $column, $value) {
		$entities = $this->findAllBy($column, $value);
		foreach ($entities as $entity) {
			try {
				$this->delete($entity);
			} catch (Exception $e) {
			}
		}

		return $entities;
	}

	/**
	 * @return Entity[]
	 */
	protected function findAllBy(string $column, $value): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq($column, $qb->createNamedParameter($value)));

		try {
			return $this->findEntities($qb);
		} catch (Exception $e) {
			return [];
		}
	}

	/**
	 * @throws MultipleObjectsReturnedException
	 */
	protected function findBy(string $column, $value): ?Entity {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq($column, $qb->createNamedParameter($value)));

		try {
			return $this->findEntity($qb);
		} catch (DoesNotExistException|Exception $e) {
			return null;
		}
	}

	public function find(int $id): ?Entity {
		try {
			return $this->findBy('id', $id);
		} catch (MultipleObjectsReturnedException $e) {
			return null;
		}
	}

	/**
	 * @return Entity[]
	 */
	public function findAll(): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')->from($this->getTableName());
		try {
			return $this->findEntities($qb);
		} catch (Exception $e) {
			return [];
		}
	}
}
