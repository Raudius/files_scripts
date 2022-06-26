<?php

namespace OCA\FilesScripts\Settings;

use OCA\FilesScripts\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\Util;

class AdminSettings implements ISettings {
	public function getForm(): TemplateResponse {
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
