<?php

declare(strict_types=1);

namespace OCA\FilesScripts\Listener;

use OCA\Files_Sharing\Event\ShareLinkAccessedEvent;
use OCA\FilesScripts\AppInfo\Application;
use OCP\AppFramework\Services\IInitialState;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

class ShareLinkAccessedListener implements IEventListener {
	private IInitialState $initialStateService;

	public function __construct(
		IInitialState $initialStateService
	) {
		$this->initialStateService = $initialStateService;
	}
	public function handle(Event $event): void {
		if (!($event instanceof ShareLinkAccessedEvent)) {
			return;
		}

		$this->initialStateService->provideInitialState('actions_in_menu', false);

		Util::addStyle(Application::APP_ID, 'global');
		Util::addScript(Application::APP_ID, 'files_scripts-main');
	}
}
