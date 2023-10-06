<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCA\Activity\Data;
use OCA\Activity\GroupHelper;
use OCA\Activity\UserSettings;
use OCA\Activity\ViewInfoCache;
use OCP\Files\IMimeTypeDetector;
use OCP\IPreview;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;


/**
 * `get_activity(Object object, [Table filters]={}): Table`
 *
 * Returns a table of activity data.
 */
class Get_Activity extends RegistrableFunction {
	use UserSerializerTrait;
	private ?IUser $user;
	private ?Data $activityData;
	private ?GroupHelper $helper;
	private ?UserSettings $settings;
	private ?ViewInfoCache $infoCache;
	private IURLGenerator $urlGenerator;
	private IPreview $preview;
	private IMimeTypeDetector $mimeTypeDetector;
	private IUserManager $userManager;

	public function __construct(
		?IUser $user,
		IUserManager $userManager,
		?Data $data,
		?GroupHelper $helper,
		?UserSettings $settings,
		?ViewInfoCache $infoCache,
		IURLGenerator $urlGenerator,
		IPreview $preview,
		IMimeTypeDetector $mimeTypeDetector
	) {
		$this->user = $user;
		$this->userManager = $userManager;
		$this->activityData = $data;
		$this->helper = $helper;
		$this->settings = $settings;
		$this->urlGenerator = $urlGenerator;
		$this->preview = $preview;
		$this->mimeTypeDetector = $mimeTypeDetector;
		$this->infoCache = $infoCache;
	}

	public function run($object=[], $filters=[]): array {
		if (!class_exists(\OCA\Activity\Data::class)) {
			return [];
		}

		$user = $this->getUser($filters);
		if (!$user) {
			return [];
		}

		$objectType = $this->getObjectType($object);
		if (!$objectType) {
			return [];
		}

		$objectId = $object['id'] ?? null;
		if (null == $objectId) {
			return [];
		}

		$activities = $this->activityData->get(
			$this->helper,
			$this->settings,
			$this->user,
			0,
			0,
			'desc',
			'all',
			$objectType,
			$objectId
		);
		return $this->reindex($activities);
	}

	private function getUser($filters): ?IUser {
		$user = null;
		$userData = $filters['user'] ?? null;
		if ($userData) {
			$user = $this->deserializeUser($userData, $this->userManager);
		}

		if (!$user) {
			$user = $this->user;
		}

		return $user;
	}

	private function getObjectType($object) {
		$type = $object['_type'] ?? '';

		switch ($type) {
			case 'file':
				return 'files'; // TODO: Look for OCA constant of object types
			default:
				return '';
		}
	}
}
