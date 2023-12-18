<?php

namespace OCA\FilesScripts\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use Psr\Log\LoggerInterface;

class Version040000Date20231217 extends SimpleMigrationStep {
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

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

		if ($schema->hasTable('filescripts')) {
			$table = $schema->getTable('filescripts');

			$table->addColumn('show_in_context', 'boolean', [
				'notnull' => false,
				'default' => false
			]);
		} else {
			$this->logger->error('File scripts (Version040000Date20231217) migration failed, because `filescripts` table does not exist.');
			throw new SchemaException("File scripts (Version040000Date20231217) migration failed, because `filescripts` table does not exist.");
		}
		return $schema;
	}
}
