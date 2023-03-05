<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\IConfig;
use OCP\IRequest;

class SettingsController extends Controller {
	private IConfig $config;

	public function __construct(
		$appName,
		IRequest $request,
		IConfig $config
	) {
		parent::__construct($appName, $request);
		$this->config = $config;
	}

	public function index(): Response {
		return new JSONResponse([], Http::STATUS_NOT_IMPLEMENTED);
	}

	public function modify(string $name, string $value): Response {
		if (!in_array($value, ['true', 'false'], true)) {
			return new JSONResponse(['error' => 'Value can only be true or false'], Http::STATUS_BAD_REQUEST);
		}

		$success = new JSONResponse([], Http::STATUS_OK);
		switch ($name) {
			case Application::APP_CONFIG_USE_PHP_INTERPRETER:
				$this->config->setAppValue(Application::APP_ID, Application::APP_CONFIG_USE_PHP_INTERPRETER, $value);
				return $success;
			case Application::APP_CONFIG_ACTIONS_IN_MENU:
				$this->config->setAppValue(Application::APP_ID, Application::APP_CONFIG_ACTIONS_IN_MENU, $value);
				return $success;
			default:
				return new JSONResponse(['error' => 'Unknown option with name: ' . $name], Http::STATUS_BAD_REQUEST);
		}
	}
}
