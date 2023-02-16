<?php

namespace OCA\FilesScripts\Settings;

use OCA\FilesScripts\AppInfo\Application;
use OCA\FilesScripts\Interpreter\Lua\LuaProvider;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;
use OCP\Util;

class AdminSettings implements ISettings {
	private IConfig $config;
	private IInitialState $initialStateService;

	public function __construct(
		IConfig $config,
		IInitialState $initialStateService
	) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
	}

	public function getForm(): TemplateResponse {
		$usePhpInterpreter = $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_USE_PHP_INTERPRETER, 'false') === 'true';
		$actionsInMenu = $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_ACTIONS_IN_MENU, 'false') === 'true';
		$this->initialStateService->provideInitialState('use_php_interpreter', $usePhpInterpreter);
		$this->initialStateService->provideInitialState('actions_in_menu', $actionsInMenu);
		$this->initialStateService->provideInitialState('lua_plugin_available', LuaProvider::isLuaPluginAvailable());

		Util::addScript(Application::APP_ID, 'files_scripts-main');
		return new TemplateResponse(Application::APP_ID, 'settings-admin');
	}

	public function getSection(): string {
		return 'files_scripts';
	}

	public function getPriority(): int {
		return 0;
	}
}
