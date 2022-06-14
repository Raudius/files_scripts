<?php
namespace OCA\FilesScripts\AppInfo;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\FilesScripts\Listener\LoadAdditionalListener;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'files_scripts';

	public function __construct() {
		parent::__construct(self::APP_ID);
		require_once(__DIR__  . '/../../vendor/autoload.php');
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, LoadAdditionalListener::class);
	}

	public function boot(IBootContext $context): void {}
}
