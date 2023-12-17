<?php

namespace OCA\FilesScripts\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use Psr\Log\LoggerInterface;

class Version031000Date20231211 extends SimpleMigrationStep {
	private LoggerInterface $logger;
	private IDBConnection $connection;

	public function __construct(LoggerInterface $logger, IDBConnection $connection) {
		$this->logger = $logger;
		$this->connection = $connection;
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

			$table->addColumn('file_types', 'string', [
				'notnull' => false,
				'length' => 128 * 30
			]);
		} else {
			$this->logger->error('File scripts (Version031000Date20231211) migration failed, because `filescripts` table does not exist.');
		}
		return $schema;
	}

	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();
		if (!$schema->hasTable('filescripts')) {
			$this->logger->error('File scripts (Version030000Date20230324) migration postschemachange step failed, because `filescripts` table does not exist.');
			return null;
		}
		/** @var \Doctrine\DBAL\Schema\Table $table */
		$table = $schema->getTable('filescripts');
		if (!$table->hasColumn('file_types')) {
			$this->logger->error('File scripts (Version030000Date20230324) migration postschemachange step failed, because `filescripts.file_types` column does not exist.');
			return null;
		}

		try {
			$qb = $this->connection->getQueryBuilder();
			$qb->update('filescripts')
				->set('file_types', 'mimetype');
			$qb->executeStatement();


			$qb = $this->connection->getQueryBuilder();
			$eb = $this->connection->getQueryBuilder()->expr();

			$qb->update('filescripts')
				->where($eb->eq('file_types', $eb->literal("")))
				->set('file_types', 'null');
			$qb->executeStatement();
		} catch (SchemaException $e) {
			throw $e;
		} catch (\Throwable $e) {
			$this->logger->error('File scripts (Version030000Date20230324) migration postschemachange step failed with exception: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
			return null;
		}

		return $schema;
	}
}
