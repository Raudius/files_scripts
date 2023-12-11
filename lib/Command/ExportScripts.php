<?php
namespace OCA\FilesScripts\Command;

use OC\Core\Command\Base;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Service\ScriptService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportScripts extends Base {
	private ScriptMapper $scriptMapper;
	private ScriptService $scriptService;

	public function __construct(
		ScriptMapper $scriptMapper,
		ScriptService $scriptService
	)  {
		parent::__construct('files_scripts:export');
		$this->scriptMapper = $scriptMapper;
		$this->scriptService = $scriptService;
	}

	protected function configure(): void {
		$this->setDescription('Exports file actions as JSON');
		$this->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'The ID of the script to be exported, if not specified all scripts will be exported.');
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)  {
		$scriptId = $input->getOption('id');
		$scriptId = $scriptId ? intval($scriptId) : null;
		if ($scriptId !== null) {
			$json = $this->exportScript($scriptId, $output);
		} else {
			$json = $this->exportAllScripts();
		}

		$output->writeln($json);
		return 0;
	}

	private function exportScript(int $scriptId, OutputInterface $output): string {
		$script = $this->scriptMapper->find($scriptId);
		if (null === $script) {
			$output->writeln('<error>Could not find script.</error>');
			return '';
		}

		$scriptJson = $this->scriptService->scriptToJson($script);
		return json_encode($scriptJson) ?: '';
	}


	private function exportAllScripts(): string {
		$allScriptsJson = [];
		$scripts = $this->scriptMapper->findAll();
		foreach ($scripts as $script) {
			$allScriptsJson[] = $this->scriptService->scriptToJson($script);
		}

		return json_encode($allScriptsJson) ?: "";
	}
}
