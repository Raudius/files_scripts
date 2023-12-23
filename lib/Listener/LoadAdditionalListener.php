<?php

declare(strict_types=1);

namespace OCA\FilesScripts\Listener;

use OCP\AppFramework\Services\IInitialState;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\FilesScripts\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;
use OCP\IConfig;

class LoadAdditionalListener implements IEventListener {

	private IConfig $config;
	private IInitialState $initialStateService;

	public function __construct(
		IConfig $config,
		IInitialState $initialStateService
	) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
	}

	public function handle(Event $event): void {
		if (!($event instanceof LoadAdditionalScriptsEvent)) {
			return;
		}

		Util::addStyle(Application::APP_ID, 'global');
		Util::addScript(Application::APP_ID, 'files_scripts-main');
	}
}
