<?php
namespace OCA\FilesScripts\Command;

use OC\Core\Command\Base;
use OCA\FilesScripts\Db\ScriptMapper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListScripts extends Base {
	private ScriptMapper $scriptMapper;

	public function __construct(
		ScriptMapper $scriptMapper
	)  {
		parent::__construct('files_scripts:list');
		$this->scriptMapper = $scriptMapper;
	}

	protected function configure(): void {
		$this->setDescription('Lists all file actions');
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output)  {
		$output->writeln('<info>Listing file actions:</info>');
		$scripts = $this->scriptMapper->findAll();

		$output->writeln("<info>id\tname</info>");
		foreach ($scripts as $script) {
			$output->writeln($script->getId() .  "\t" . $script->getTitle() );
		}
		return 0;
	}
}
