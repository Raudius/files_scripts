<?php
namespace OCA\FilesScripts\Command;

use OC\Core\Command\Base;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Interpreter\Context;
use OCA\FilesScripts\Interpreter\Lua\LuaProvider;
use OCA\FilesScripts\Service\ScriptService;
use OCP\Files\IRootFolder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunScript extends Base {
	private ScriptService $scriptService;
	private ScriptMapper $scriptMapper;
	private ScriptInputMapper $scriptInputMapper;
	private LuaProvider $luaProvider;
	private IRootFolder $rootFolder;

	public function __construct(
		ScriptService $scriptService,
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper,
		IRootFolder $rootFolder,
		LuaProvider $luaProvider
	)  {
		parent::__construct('files_scripts:run');
		$this->scriptService = $scriptService;
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
		$this->rootFolder = $rootFolder;
		$this->luaProvider = $luaProvider;
	}

	protected function configure(): void {
		$this->setDescription('Runs a file action')
			->addArgument('id', InputArgument::REQUIRED, 'ID of the action to be run')
			->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'User as which the action should be run')
			->addOption('inputs', 'i', InputOption::VALUE_OPTIONAL, 'The user inputs to be set before running the action as a JSON string')
			->addOption('file', 'f', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'File path or id of a file given to the action as input file');
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)  {
		$scriptId = $input->getArgument('id');
		$userId = $input->getOption('user');
		$scriptInputsJson = $input->getOption('inputs') ?? '{}';
		$fileInputs = $input->getOption('file') ?? [];
		
		try {
			$scriptInputsData = json_decode($scriptInputsJson, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $err) {
			$output->writeln('<error>Could not parse the inputs JSON</error>');
			return 1;
		}

		$script = $this->scriptMapper->find($scriptId);
		$output->writeln('<info>Executing file action: ' . $script->getTitle() . '</info>');

		$scriptInputs = $this->scriptInputMapper->findAllByScriptId($scriptId);
		foreach ($scriptInputs as $scriptInput) {
			$value = $scriptInputsData[$scriptInput->getName()] ?? null;
			$scriptInput->setValue($value);
		}

		$rootFolder = $this->rootFolder->getUserFolder($userId);

		$files = [];
		$n = 1;
		foreach ($fileInputs as $fileInput) {
			if (isset($fileInput))
			{
				try {
					if (ctype_digit(strval($fileInput))) {
						$nodes = $rootFolder->getById(intval($fileInput));
						if (!isset($nodes[0])) {
							$output->writeln('<error>Could not find input file ' . $fileInput . ' belonging in root folder ' . $rootFolder->getPath() . ' for file action</error>');			
							return 1;
						}
						$file = $nodes[0];
						unset($nodes);
					} else {
						$file = $rootFolder->get($fileInput);
					}
				} catch (\Exception $e) {
					$output->writeln('<error>Could not find input file ' . $fileInput . ' belonging in root folder ' . $rootFolder->getPath() . ' for file action</error>');
					return 1;
				}
				$files[$n++] = $file;
			}
		}

		$context = new Context(
			$this->luaProvider->createLua(),
			$rootFolder,
			$scriptInputs,
			$files
		);

		$this->scriptService->runScript($script, $context);


		$output->writeln('<info>File action executed successfully.</info>');
		return 0;
	}
}
