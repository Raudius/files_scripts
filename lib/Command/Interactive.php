<?php
namespace OCA\FilesScripts\Command;

use OC\Core\Command\Base;
use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Interpreter\Context;
use OCA\FilesScripts\Interpreter\Interpreter;
use OCA\FilesScripts\Interpreter\Lua\LuaProvider;
use OCP\Files\IRootFolder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Interactive extends Base {
	private LuaProvider $luaProvider;
	private IRootFolder $rootFolder;
	private Interpreter $interpreter;

	public function __construct(
		IRootFolder $rootFolder,
		LuaProvider $luaProvider,
		Interpreter $interpreter
	) {
		parent::__construct('files_scripts:interactive');
		$this->rootFolder = $rootFolder;
		$this->luaProvider = $luaProvider;
		$this->interpreter = $interpreter;
	}
	protected function configure(): void {
		$this->setDescription('Starts an interactive Lua shell where you can interact with the server using the scripting API')
			->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'User as which the action should be run')
			->addOption('file', 'f', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'File path or id of a file given to the action as input file');

		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$userId = $input->getOption('user');
		$rootFolder = $this->rootFolder;

		try {
			if ($userId) {
				$rootFolder = $this->rootFolder->getUserFolder($userId);
			}
			$files = RunScript::getFilesForCommand($input, $output, $rootFolder);
		} catch (\Throwable $e) {
			$output->writeln('<error>' . $e->getMessage() .'</error>');
			return 1;
		}

		$output->writeln('<info>Lua files_scripts interpreter started...</info>');
		$output->writeln('<info>To stop type "exit"</info>');

		$context = new Context($this->luaProvider->createLua(), $rootFolder, [], $files);
		$f = fopen( 'php://stdin', 'r' );
		$command = "";
		while (true) {
			echo "> ";
			$line = fgets( $f );

			// Handle exit clause
			if (trim($line) == "exit") {
				fclose($f);
				break;
			}

			$replacements = 0;
			$line = preg_replace('/(.*)\\\\(\s*)$/i', '$1 $2', $line, 1, $replacements);
			$command .= $line;

			// if line does not end with `\` backslash we execute the command
			if ($replacements == 0) {
				$this->executeCommand($command, $context, $output);
				$command = "";
			}
		}

		return 0;
	}

	private function executeCommand(string $command, Context $context, OutputInterface $output): void {
		$script = new Script();
		$script->setProgram($command);
		try {
			$this->interpreter->execute($script, $context);
		} catch (\Throwable $e) {
			$output->writeln('<error>' . $e->getMessage() . '</error>');
		}
	}
}
