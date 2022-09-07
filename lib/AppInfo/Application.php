<?php

namespace OCA\FilesScripts\AppInfo;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\FilesScripts\Listener\LoadAdditionalListener;
use OCA\FilesScripts\Listener\RegisterFlowOperationsListener;
use OCA\FilesScripts\Middleware\DefaultScriptsMiddleware;
use OCA\FilesScripts\Service\Notifier;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;

class Application extends App implements IBootstrap {
	public const APP_ID = 'files_scripts';

	public function __construct() {
		parent::__construct(self::APP_ID);
		require_once(__DIR__  . '/../../vendor/autoload.php');
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, LoadAdditionalListener::class);
		$context->registerEventListener(RegisterOperationsEvent::class, RegisterFlowOperationsListener::class);
		$context->registerMiddleware(DefaultScriptsMiddleware::class);
		$context->registerNotifierService(Notifier::class);
	}

	public function boot(IBootContext $context): void {
	}
}
