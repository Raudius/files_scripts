<?php

namespace OCA\FilesScripts\AppInfo;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\Files_Sharing\Event\ShareLinkAccessedEvent;
use OCA\FilesScripts\Event\RegisterScriptFunctionsEvent;
use OCA\FilesScripts\Listener\RegisterScriptFunctionListener;
use OCA\FilesScripts\Interpreter\FunctionProvider;
use OCA\FilesScripts\Interpreter\IFunctionProvider;
use OCA\FilesScripts\Listener\LoadAdditionalListener;
use OCA\FilesScripts\Listener\RegisterFlowOperationsListener;
use OCA\FilesScripts\Listener\ShareLinkAccessedListener;
use OCA\FilesScripts\Middleware\DefaultScriptsMiddleware;
use OCA\FilesScripts\Service\Notifier;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;

class Application extends App implements IBootstrap {
	public const APP_ID = 'files_scripts';

	public const APP_CONFIG_FIRST_RUN = 'first_run';
	public const APP_CONFIG_USE_PHP_INTERPRETER = 'php_interpreter';
	public const APP_CONFIG_ACTIONS_IN_MENU = 'actions_in_menu';

	public function __construct() {
		parent::__construct(self::APP_ID);
		require_once(__DIR__  . '/../../vendor/autoload.php');
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, LoadAdditionalListener::class);
		$context->registerEventListener(RegisterOperationsEvent::class, RegisterFlowOperationsListener::class);
		$context->registerMiddleware(DefaultScriptsMiddleware::class);
		$context->registerNotifierService(Notifier::class);
		$context->registerEventListener(ShareLinkAccessedEvent::class, ShareLinkAccessedListener::class);
		$context->registerEventListener(RegisterScriptFunctionsEvent::class, RegisterScriptFunctionListener::class);

		$context->registerService(IFunctionProvider::class, function () {
			return \OC::$server->get(FunctionProvider::class);
		});
	}

	public function boot(IBootContext $context): void {
	}
}
