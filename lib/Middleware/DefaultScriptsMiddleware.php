<?php

namespace OCA\FilesScripts\Middleware;

use OCA\FilesScripts\AppInfo\Application;
use OCA\FilesScripts\Controller\ScriptController;
use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCP\AppFramework\Middleware;
use OCP\DB\Exception;
use OCP\IConfig;
use Psr\Log\LoggerInterface;

class DefaultScriptsMiddleware extends Middleware {
	private const DEFAULT_SCRIPTS = [
		[
			'program' => '../../examples/business_card.lua',
			'name' => 'Generate business card',
			'description' => 'Generates a business card PDF. This script will create the file "business-card.pdf" in your home folder.',
			'inputs' => [
				'company_name' => 'Company name',
				'phone' => 'Phone number',
				'email' => 'Email address',
				'name' => 'Full name',
				'title' => [
					'description' => 'Occupation',
					'options' => [
						'type' => 'multiselect',
						'multiselectOptions' => [ 'Sales associate', 'Marketing', 'Software developer']
					]
				],
				'website' => 'Web URL',
			]
		],
		[
			'program' => '../../examples/merge_pdfs.lua',
			'name' => 'Merge PDFs',
			'description' => 'Combines all the selected PDFs into a single file.',
			'inputs' => [
				'file_name' => 'Name of the output file',
				'output_location' => [
					'description' => 'Save location',
					'options' => [
						'type' => 'filepick',
						'filepickMimes' => ['httpd/unix-directory']
					]
				]
			],
			'mimetype' => 'application/pdf',
		],
		[
			'program' => '../../examples/directory_tree.lua',
			'name' => 'Directory tree',
			'description' => 'Creates a file "tree.txt" containing the recursive directory listing of the selected files and folders.',
		],
		[
			'program' => '../../examples/generate_invoice.lua',
			'name' => 'Generate invoice',
			'description' => 'Creates an invoice from valid JSON files containing order data (see script comments for more details).',
			'inputs' => [
				'output_location' => [
					'description' => 'Save location',
					'options' => [
						'type' => 'filepick',
						'filepickMimes' => ['httpd/unix-directory']
					]
				]
			]
		],
	];

	private ScriptMapper $scriptMapper;
	private IConfig $config;
	private ScriptInputMapper $scriptInputMapper;
	private LoggerInterface $logger;

	public function __construct(
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper,
		IConfig $config,
		LoggerInterface $logger
	) {
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
		$this->config = $config;
		$this->logger = $logger;
	}

	public function beforeController($controller, $methodName): void {
		if ($controller instanceof ScriptController) {
			$this->createDefaultScripts();
		}
	}

	private function isFirstRun(): bool {
		return $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_FIRST_RUN, 'true') === 'true'
			&& empty($this->scriptMapper->findAll());
	}

	private function createDefaultScripts(): void {
		if (!$this->isFirstRun()) {
			return;
		}

		$this->config->setAppValue(Application::APP_ID, Application::APP_CONFIG_FIRST_RUN, 'false');

		foreach (self::DEFAULT_SCRIPTS as $scriptData) {
			try {
				$this->createDefaultScript($scriptData);
			} catch (Exception $e) {
				$this->logger->error('Files scripts could not create default script', [
					'error_message' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
					'script' => $scriptData['name']
				]);
			}
		}
	}

	/**
	 * @param array $scriptData
	 * @return void
	 * @throws Exception
	 */
	private function createDefaultScript(array $scriptData): void {
		$program = file_get_contents(__DIR__ . '/' .$scriptData['program']) ?: null;
		if (!$program) {
			return;
		}

		$script = new Script();
		$script->setProgram($program);
		$script->setEnabled(false);
		$script->setTitle($scriptData['name']);
		$script->setDescription($scriptData['description']);
		$script->setMimetype($scriptData['mimetype'] ?? '');

		$script = $this->scriptMapper->insert($script);

		$inputData = $scriptData['inputs'] ?? [];
		foreach ($inputData as $name => $data) {
			if (is_string($data)) {
				$data = [ 'description' => $data ];
			}

			$scriptInput = new ScriptInput();
			$scriptInput->setScriptId($script->getId());
			$scriptInput->setName($name);
			$scriptInput->setDescription($data['description']);
			$scriptInput->setScriptOptions($data['options'] ?? []);

			$this->scriptInputMapper->insert($scriptInput);
		}
	}
}
