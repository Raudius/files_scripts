<?php
namespace OCA\FilesScripts\Interpreter\Lua;

use OCA\FilesScripts\AppInfo\Application;
use OCP\IConfig;

class LuaProvider {
	private IConfig $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	public function createLua(): LuaWrapper {
		$usePhpLua = (bool) $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_USE_PHP_INTERPRETER, false);
		if ($usePhpLua) {
			return new LuarInterpreter();
		}

		if (!class_exists(\Lua::class)) {
			throw new \RuntimeException('No Lua interpreter available.');
		}

		return new LuaInterpreter();
	}

	public function isAvailable(): bool {
		$usePhpLua = (bool) $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_USE_PHP_INTERPRETER, false);
		return $usePhpLua || class_exists(\Lua::class);
	}
}
