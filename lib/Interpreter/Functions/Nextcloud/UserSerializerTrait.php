<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\IUser;
use OCP\IUserManager;

/**
 * Trait which manages User (de)serialization..
 */
trait UserSerializerTrait {
	// TODO: PHP 8.2 add trait const for TYPE="user"
	private function serializeUser(IUser $user): array {
		return [
			'_type' => 'user',
			'uuid' => $user->getUID(),
			'display_name' => $user->getDisplayName(),
			'email_address' => $user->getEMailAddress(),
		];
	}

	private function deserializeUser($userData, IUserManager $userManager): ?IUser {
		if (!is_array($userData) || ($userData["_type"] ?? null) !== "user") {
			return null;
		}

		try {
			$user = $userManager->get($userData['uuid'] ?? '');
		} catch (\Throwable $e) {
			return null;
		}
		return $user;
	}
}
