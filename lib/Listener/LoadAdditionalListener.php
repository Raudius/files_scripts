<?php

declare(strict_types=1);

namespace OCA\FilesScripts\Listener;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\FilesScripts\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

class LoadAdditionalListener implements IEventListener {
	public function handle(Event $event): void {
		if (!($event instanceof LoadAdditionalScriptsEvent)) {
			return;
		}

		Util::addStyle(Application::APP_ID, 'global');
		Util::addScript(Application::APP_ID, 'files_scripts-main');
	}
}
