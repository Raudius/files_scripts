<?php

namespace OCA\FilesScripts\Middleware;

use OCA\FilesScripts\AppInfo\Application;
use OCA\FilesScripts\Controller\ScriptController;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Service\ScriptService;
use OCP\AppFramework\Middleware;
use OCP\DB\Exception;
use OCP\IConfig;
use Psr\Log\LoggerInterface;

class DefaultScriptsMiddleware extends Middleware {
	private ScriptService $scriptService;
	private ScriptMapper $scriptMapper;
	private IConfig $config;
	private LoggerInterface $logger;

	public function __construct(
		ScriptService $scriptService,
		ScriptMapper $scriptMapper,
		IConfig $config,
		LoggerInterface $logger
	) {
		$this->scriptMapper = $scriptMapper;
		$this->scriptService = $scriptService;
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

		$scriptsJson = file_get_contents(__DIR__ . '/examples.json');
		if (!$scriptsJson) {
			$this->logger->warning('Could not create default scripts, examples.json not found.');
			return;
		}

		$scriptsJsonData = json_decode($scriptsJson, true);
		if (!$scriptsJsonData) {
			$this->logger->warning('Could not create default scripts, could not decode JSON.');
			return;
		}
		$this->config->setAppValue(Application::APP_ID, Application::APP_CONFIG_FIRST_RUN, 'false');

		foreach ($scriptsJsonData as $scriptData) {
			try {
				$this->scriptService->createScriptFromJson($scriptData);
			} catch (Exception $e) {
				$this->logger->error('Files scripts could not create default script', [
					'error_message' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
					'script' => $scriptData['title'] ?? 'unknown title',
					'script_data' => $scriptData,
				]);
			}
		}
	}
}
