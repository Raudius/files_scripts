<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\AbortException;
use OCP\IUser;
use OCP\IUserManager;

/**
 * Trait which manages User (de)serialization..
 */
trait UserSerializerTrait {
	private function serializeUser(IUser $user): array {
		return [
			'_type' => 'user',
			'uuid' => $user->getUID(),
			'display_name' => $user->getDisplayName(),
			'email_address' => $user->getEMailAddress(),
		];
	}

	private function deserializeUser(array $userData, IUserManager $userManager): ?IUser {
		try {
			$user = $userManager->get($userData['uuid'] ?? '');
		} catch (\Throwable $e) {
			throw new AbortException($e->getMessage());
			return null;
		}
		return $user;
	}

}
