<?php

namespace OCA\FilesScripts\Service;

use OCA\FilesScripts\AppInfo\Application;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;

class Notifier implements INotifier {
	private IFactory $l10nFactory;
	private IURLGenerator $url;

	public function __construct(
		IFactory $l10nFactory,
		IURLGenerator $urlGenerator
	) {
		$this->l10nFactory = $l10nFactory;
		$this->url = $urlGenerator;
	}

	public function getID(): string {
		return Application::APP_ID;
	}

	public function getName(): string {
		return $this->l10nFactory->get(Application::APP_ID)->t('File actions');
	}

	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== Application::APP_ID) {
			throw new \InvalidArgumentException();
		}

		$notification->setParsedSubject($notification->getSubject());
		$notification->setParsedMessage($notification->getMessage());
		$notification->setIcon($this->url->getAbsoluteURL($this->url->imagePath('files_scripts', 'files_scripts-dark.svg')));
		return $notification;
	}
}
