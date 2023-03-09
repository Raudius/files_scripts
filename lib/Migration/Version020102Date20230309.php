<?php

namespace OCA\FilesScripts\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use Psr\Log\LoggerInterface;

/**
 * Extends the script input .
 */
class Version020102Date20230309 extends SimpleMigrationStep {
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

			$table->dropColumn('background');
			$table->addColumn('limit_groups', 'string', [
				'notnull' => false,
				'default' => '',
				'length' => 1000
			]);
		} else {
			$this->logger->error('File scripts (Version020102Date20230309) migration failed, because `filescripts` table does not exist.');
		}
		return $schema;
	}
}
