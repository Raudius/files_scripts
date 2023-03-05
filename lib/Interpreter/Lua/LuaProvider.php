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
		$usePhpLua = $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_USE_PHP_INTERPRETER, 'false') === 'true';

		if ($usePhpLua) {
			return new LuarInterpreter();
		}

		if (!class_exists(\Lua::class)) {
			throw new \RuntimeException('No Lua interpreter available.');
		}

		return new LuaInterpreter();
	}

	public function isAvailable(): bool {
		$usePhpLua = $this->config->getAppValue(Application::APP_ID, Application::APP_CONFIG_USE_PHP_INTERPRETER, 'false') === 'true';
		return $usePhpLua || self::isLuaPluginAvailable();
	}

	public static function isLuaPluginAvailable(): bool {
		return class_exists(\Lua::class);
	}
}
