<?php

namespace OCA\FilesScripts\Db;

use OCP\IDBConnection;

/**
 * @method ScriptInput findBy(string $column, string $value)
 * @method ScriptInput[] findAllBy(string $column, string $value)
 * @method ScriptInput find(int $id)
 * @method ScriptInput[] findAll()
 */
class ScriptInputMapper extends BaseMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'filescript_inputs', ScriptInput::class);
	}

	/**
	 * @return ScriptInput[]
	 */
	public function deleteByScriptId(int $scriptId): array {
		return $this->deleteBy('script_id', $scriptId);
	}

	/**
	 * @return ScriptInput[]
	 */
	public function findAllByScriptId(int $scriptId): array {
		return $this->findAllBy('script_id', $scriptId);
	}
}
