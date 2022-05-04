<?php

namespace OCA\FilesScripts\Settings;

use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {

	private IURLGenerator $urlGenerator;

	public function __construct(IURLGenerator $urlGenerator) {
		$this->urlGenerator = $urlGenerator;
	}

	public function getID(): string {
		return 'files_scripts';
	}

	public function getName(): string {
		return 'Custom actions';
	}

	public function getPriority(): int {
		return 80;
	}

	public function getIcon() {
		return $this->urlGenerator->imagePath('files_scripts', 'app.svg');
	}
}
