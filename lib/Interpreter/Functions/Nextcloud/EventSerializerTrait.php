<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\Activity\Data;
use OCP\Activity\IEvent;

/**
 * Trait which manages Event (de)serialization..
 */
trait EventSerializerTrait {
	// TODO: PHP 8.2 add trait const for TYPE="user"
	private function serializeEvent(int $id, IEvent $event): array {
		return [
			"_type" => "event",
			"id" => $id,
			'app' => $event->getApp(),
			'type' => $event->getType(),
			'affecteduser' => $event->getAffectedUser(),
			'user' => $event->getAuthor(),
			'timestamp' => $event->getTimestamp(),
			'subject' => $event->getParsedSubject(),
			'message' => $event->getParsedMessage(),
			'object_type' => $event->getObjectType(),
			'object_id' => $event->getObjectId(),
			'object_name' => $event->getObjectName(),
			'link' => $event->getLink(),
		];
	}

	private function deserializeEvent($activityData, Data $activityManager): ?IEvent {
		if (!is_array($activityData) || ($userData["_type"] ?? null) !== "event") {
			return null;
		}

		try {
			$activity = $activityManager->getById($activityData["id"]);
		} catch (\Throwable $e) {
			return null;
		}
		return $activity;
	}
}
