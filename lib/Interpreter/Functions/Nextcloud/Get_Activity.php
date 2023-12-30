<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\Files\IMimeTypeDetector;
use OCP\IPreview;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\IUserSession;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;


/**
 * `get_activity(object Node): Event[]`
 *
 * Returns a table of activity data for the given object. Currently, only `Node` objects may be used for retrieving activity.
 *
 * If the activity app is not installed or enabled, this function returns an empty table.
 *
 * Example:
 * ```lua
 * file = get_input_files()[1]
 * activity = get_activity(file)
 * add_message(json(activity))
 * ```
 */
class Get_Activity extends RegistrableFunction {
	use EventSerializerTrait;
	private ?IUserSession $userSession;
	private IURLGenerator $urlGenerator;
	private IPreview $preview;
	private IMimeTypeDetector $mimeTypeDetector;
	private IUserManager $userManager;
	private ContainerInterface $container;
	private LoggerInterface $logger;

	public function __construct(
		LoggerInterface $logger,
		ContainerInterface $container,
		IUserSession $userSession,
		IUserManager $userManager,
		IURLGenerator $urlGenerator,
		IPreview $preview,
		IMimeTypeDetector $mimeTypeDetector
	) {
		$this->logger = $logger;
		$this->container = $container;
		$this->userSession = $userSession;
		$this->userManager = $userManager;
		$this->urlGenerator = $urlGenerator;
		$this->preview = $preview;
		$this->mimeTypeDetector = $mimeTypeDetector;
	}

	public function run($object=[]): array {
		if (
			!class_exists(\OCA\Activity\Data::class)
			|| !class_exists(\OCA\Activity\UserSettings::class)
			|| !class_exists(\OCA\Activity\GroupHelper::class)
		) {
			return [];
		}


		try {
			$activityData = $this->container->get(\OCA\Activity\Data::class);
			$groupHelper = $this->container->get(\OCA\Activity\GroupHelper::class);
			$userSettings = $this->container->get(\OCA\Activity\UserSettings::class);
		} catch (\Throwable $e) {
			$this->logger->error("[files_scripts] Failed to initialise activity app Data class ");
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

		/** @var \OCP\Activity\IEvent[] $activityEvents */
		$activityEvents = $activityData->get(
			$groupHelper,
			$userSettings,
			$userId,
			0,
			200,
			'desc',
			'filter',
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
	private function getObjectType($object): ?string {
		$type = $object['_type'] ?? '';

		switch ($type) {
			case 'file':
				return 'files'; // TODO: Look for OCA constant of object types
			default:
				return null;
		}
	}
}
