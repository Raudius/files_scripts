<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IUserSession;
use Throwable;

/**
 * `users_find([String name = nil], [String uuid = nil]): User[]`
 *
 * Finds a Nextcloud user from the given parameters.
 *
 * If the name is specified, the function will return all users who have a matching name. If the UUID is given the name is ignored and a user is returned with the given UUID.
 * If both parameters are left empty (`nil`), the current user is returned.
 * If a user that meets the parameters can't be found an empty array is returned.
 */
class Users_Find extends RegistrableFunction {
	use UserSerializerTrait;

	private IUserSession $userSession;
	private IUserManager $userManager;

	public function __construct(IUserSession $userSession, IUserManager $userManager) {
		$this->userSession = $userSession;
		$this->userManager = $userManager;
	}

	public function run($name = null, $uuid = null): array {
		if ($name === null && $uuid === null) {
			$users = [$this->userSession->getUser()];
		} else {
			$users = $this->findUsers($name, $uuid);
		}

		$users = array_filter(array_values($users));
		return $this->reindex(array_map([$this, 'serializeUser'], $users));
	}

	/**
	 * @return IUser[]
	 */
	private function findUsers($name, $uuid): array {
		try {
			if ($uuid) {
				return array_filter([$this->userManager->get($uuid)]);
			}

			return $this->userManager->search($name);
		} catch (Throwable $e) {
			return [];
		}
	}
}
