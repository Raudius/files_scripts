<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCA\Activity\Data;
use OCA\Activity\GroupHelper;
use OCA\Activity\UserSettings;
use OCA\Activity\ViewInfoCache;
use OCP\Activity\IEvent;
use OCP\Files\IMimeTypeDetector;
use OCP\IPreview;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\IUserSession;


/**
 * `get_activity(Object object, [Table filters]={}): Table`
 *
 * Returns a table of activity data.
 */
class Get_Activity extends RegistrableFunction {
	use EventSerializerTrait;
	private ?IUserSession $userSession;

	// FIXME: If Nextcloud includes a nice interface for fetching IEvents this property should be replaced
	private ?Data $activityData;
	private ?GroupHelper $helper;
	private ?UserSettings $settings;
	private ?ViewInfoCache $infoCache;
	private IURLGenerator $urlGenerator;
	private IPreview $preview;
	private IMimeTypeDetector $mimeTypeDetector;
	private IUserManager $userManager;

	public function __construct(
		IUserSession $userSession,
		IUserManager $userManager,
		?Data $data,
		?GroupHelper $helper,
		?UserSettings $settings,
		?ViewInfoCache $infoCache,
		IURLGenerator $urlGenerator,
		IPreview $preview,
		IMimeTypeDetector $mimeTypeDetector
	) {
		$this->userSession = $userSession;
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

		$objectType = $this->getObjectType($object);
		if (!$objectType) {
			return [];
		}

		$objectId = $object['id'] ?? null;
		if (null == $objectId) {
			return [];
		}

		$user = $this->userSession->getUser();
		$userId = $user ? $user->getUID() : null;

		/** @var IEvent[] $activityEvents */
		$activityEvents = $this->activityData->get(
			$this->helper,
			$this->settings,
			$userId,
			0,
			200,
			'desc',
			'all',
			$objectType,
			$objectId,
			true
		);

		$serializedEvents = [];
		foreach ($activityEvents as $id => $activityEvent) {
			$serializedEvents[] = $this->serializeEvent($id, $activityEvent);
		}

		return $serializedEvents;
	}

	/**
	 * Translates the internal object["_type"] into the Nextcloud object type.
	 */
	private function getObjectType($object): string {
		$type = $object['_type'] ?? 'files_scripts-unknown_type';

		switch ($type) {
			case 'file':
				return 'files'; // TODO: Look for OCA constant of object types
			default:
				return $type;
		}
	}
}
