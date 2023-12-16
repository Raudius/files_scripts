<?php

namespace OCA\FilesScripts\Migration;

use Closure;
use Doctrine\DBAL\Schema\SchemaException;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use Psr\Log\LoggerInterface;

/**
 * Ensures script input options has default.
 */
class Version030100Date20231215 extends SimpleMigrationStep {
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

		if ($schema->hasTable('filescript_inputs')) {
			$table = $schema->getTable('filescript_inputs');
			$optionsCol = $table->getColumn('options');

			$optionsDefault = $optionsCol->getDefault();
			if ($optionsDefault === null) {
				$this->logger->info("Column `options` in `filescript_inputs` has no default, setting it now.");
				$optionsCol->setDefault('[]');
			}
		} else {
			$this->logger->error('File scripts (Version030100Date20231215) migration failed, because `filescript_inputs` table does not exist.');
		}
		return $schema;
	}
}
