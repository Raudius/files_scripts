<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\AppInfo\Application;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\IUserManager;
use OCP\Notification\IManager;
use Throwable;

/**
 * `notify(User user, String subject, String message): Bool`
 *
 * Sends a simple notification to a user.
 *
 * ```lua
 * local user = users_find()[1]
 * notify(user, "Hello!", "Message goes here :)")
 * ```
 */
class Notify extends RegistrableFunction {
	use UserSerializerTrait;
	private IManager $notificationManager;
	private IUserManager $userManager;

	public function __construct(IManager $notificationManager, IUserManager $userManager) {
		$this->notificationManager = $notificationManager;
		$this->userManager = $userManager;
	}

	public function run($userData = [], $subject = '', $message = ''): bool {
		$user = is_array($userData) ? $this->deserializeUser($userData, $this->userManager) : '';
		if (!$user) {
			return false;
		}

		try {
			$notification = $this->notificationManager->createNotification()
				->setApp(Application::APP_ID)
				->setUser($user->getUID())
				->setSubject($subject)
				->setMessage($message)
				->setDateTime(date_create())
				->setObject('script', time());

			$this->notificationManager->notify($notification);
		} catch (Throwable $e) {
			return false;
		}

		return true;
	}
}
