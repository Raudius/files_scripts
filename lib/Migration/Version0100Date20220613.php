<?php

namespace OCA\FilesScripts\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

/**
 * Creates the scripts table.
 */
class Version0100Date20220613 extends SimpleMigrationStep {
	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return ISchemaWrapper
	 * @throws SchemaException
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('filescripts')) {
			$table = $schema->createTable('filescripts');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('title', 'string', [
				'notnull' => true,
				'length' => 200
			]);
			$table->addColumn('description', 'string', [
				'notnull' => true,
				'default' => '',
				'length' => 1000
			]);
			$table->addColumn('program', 'text', [
				'notnull' => true,
				'default' => ''
			]);
			$table->addColumn('enabled', 'boolean', [
				'notnull' => false,
				'default' => false
			]);
			$table->addColumn('background', 'boolean', [
				'notnull' => false,
				'default' => false
			]);
			$table->addColumn('request_directory', 'boolean', [
				'notnull' => false,
				'default' => false
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['title'], 'filescripts_title_index');
		}
		return $schema;
	}
}
