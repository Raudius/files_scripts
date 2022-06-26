<?php

namespace OCA\FilesScripts\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {
	private IURLGenerator $urlGenerator;
	private IL10N $l;

	public function __construct(IURLGenerator $urlGenerator, IL10N $l) {
		$this->urlGenerator = $urlGenerator;
		$this->l = $l;
	}

	public function getID(): string {
		return 'files_scripts';
	}

	public function getName(): string {
		return $this->l->t('File actions');
	}

	public function getPriority(): int {
		return 80;
	}

	public function getIcon(): string {
		return $this->urlGenerator->imagePath('files_scripts', 'files_scripts-dark.svg');
	}
}
