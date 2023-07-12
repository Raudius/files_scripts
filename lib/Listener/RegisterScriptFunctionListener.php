<?php

declare(strict_types=1);

namespace OCA\FilesScripts\Listener;

use OCA\FilesScripts\Event\RegisterScriptFunctionsEvent;
use OCA\FilesScripts\Interpreter\FunctionProvider;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

class RegisterScriptFunctionListener implements IEventListener {
	private FunctionProvider $functionProvider;

	public function __construct(FunctionProvider $functionProvider) {
		$this->functionProvider = $functionProvider;
	}

	public function handle(Event $event): void {
		if (!($event instanceof RegisterScriptFunctionsEvent)) {
			return;
		}

		$event->registerFunctions($this->functionProvider);
	}
}
