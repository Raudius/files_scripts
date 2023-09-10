<?php
namespace OCA\FilesScripts\Command;

use OC\Core\Command\Base;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Service\ScriptService;
use OCA\FilesScripts\Service\ScriptValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportScripts extends Base {
	private ScriptService $scriptService;

	private LoggerInterface $logger;

	public function __construct(
		ScriptService $scriptService,
		LoggerInterface $logger
	)  {
		parent::__construct('files_scripts:import');
		$this->scriptService = $scriptService;
		$this->logger = $logger;
	}

	protected function configure(): void {
		$this->setDescription('Imports file actions from JSON');
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)  {
		$json = file_get_contents("php://stdin");
		if (false === $json) {
			$output->writeln('<error>You need an interactive terminal to run this command</error>');
			return -1;
		}

		$jsonData = json_decode($json, true);
		if (!$jsonData) {
			$output->writeln('<error>Failed to parse JSON</error>');
			return -1;
		}

		$isSingleScript = !isset($jsonData[0]);
		if ($isSingleScript) {
			$jsonData = [$jsonData];
		}

		foreach ($jsonData as $scriptData) {
			try {
				$script = $this->scriptService->createScriptFromJson($scriptData);
				$output->writeln('<info>Imported script: ' . $script->getTitle() . '</info>');
			} catch (ScriptValidationException $e) {
				$output->writeln('<error>' . $e->getMessage() . '</error>');
			} catch (\Exception $e) {
				$output->writeln('<info>FAILED TO IMPORT SCRIPT: ' . ($scriptData['title'] ?? '<unknown-title>') . '</info>');
				$this->logger->warning('Failed to import script', [
					'script_data' => $scriptData,
					'error_message' => $e->getMessage(),
					'error_trace' => $e->getTraceAsString()
				]);
			}
		}

		return 0;
	}

}
