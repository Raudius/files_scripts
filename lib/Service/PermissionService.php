<?php
namespace OCA\FilesScripts\Service;

use OCA\FilesScripts\Db\Script;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;


class PermissionService {
	private IGroupManager $groupManager;
	private IUserSession $userSession;

	/**
	 * Cache for groups each user belongs to.
	 * @var array
	 */
	private array $groupMaps;

	public function __construct(IGroupManager $groupManager, IUserSession $userSession) {
		$this->groupManager = $groupManager;
		$this->userSession = $userSession;
		$this->groupMaps = [];
	}

	public function isUserAdmin(): bool {
		return $this->groupManager->isAdmin($this->userSession->getUser()->getUID());
	}

	/**
	 * Checks if the given script is allowed to be executed by the current user.
	 */
	public function isEnabledForUser(Script $script): bool {
		if (!$script->getEnabled()) {
			return false;
		}

		$user = $this->userSession->getUser();
		if ($user === null) {
			return $script->getPublic(); // No user => Only public scripts
		}

		$limitGroups = $script->getLimitGroupsArray();
		if (empty($limitGroups)) {
			return true;
		}

		$groupMap = $this->getGroupMap($user);
		foreach ($limitGroups as $group) {
			if (isset($groupMap[$group])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the scripts which can be executed by the current suer.
	 *
	 * @param Script[] $scripts
	 * @return Script[]
	 */
	public function filterAllowedScripts(array $scripts): array {
		return array_filter($scripts, [$this, 'isEnabledForUser']);
	}

	/**
	 * Returns the group map for a user.
	 *
	 * @param IUser|null $user
	 * @return array
	 */
	public function getGroupMap(?IUser $user): array {
		if (!$user) {
			return [];
		}

		$userInMap = isset($this->groupMaps[$user->getUID()]);
		if (!$userInMap) {
			$groupIds = $this->groupManager->getUserGroupIds($user);
			$groupMap = [];
			foreach ($groupIds as $groupId) {
				$groupMap[$groupId] = true;
			}

			$this->groupMaps[$user->getUID()] = $groupMap;
		}

		return $this->groupMaps[$user->getUID()];
	}

}
