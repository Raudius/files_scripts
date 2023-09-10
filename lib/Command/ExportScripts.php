<?php
namespace OCA\FilesScripts\Command;

use OC\Core\Command\Base;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Service\ScriptService;
use Symfony\Component\Console\Input\InputInterface;
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
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)  {
		$scriptId = $input->hasArgument("id")
			? intval($input->getArgument('id'))
			: null;
		if ($scriptId !== null) {
			$json = $this->exportScript($scriptId);
		} else {
			$json = $this->exportAllScripts();
		}

		$output->writeln($json);
		return 0;
	}

	private function exportScript(int $scriptId) {
		$script = $this->scriptMapper->find($scriptId);
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
