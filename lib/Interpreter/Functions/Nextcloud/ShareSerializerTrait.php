<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Share\IManager;
use OCP\Share\IShare;

/**
 * Trait which manages Tag (de)serialization..
 */
trait ShareSerializerTrait {
	private function serializeShare(IShare $share): array {
		return [
			'_type' => 'share',
			'id' => $share->getId(),
			'full_id' => $share->getFullId(),
			'type' => $share->getShareType(),
			'share_owner' => $share->getShareOwner(),
			'shared_by' => $share->getSharedBy(),
			'shared_with' => $share->getSharedBy(),
			'permissions' => $share->getPermissions(),
			'token' => $share->getToken(),
		];
	}

	private function deserializeShare(array $shareData, IManager $shareManager): ?IShare {
		try {
			return $shareManager->getShareById($shareData['full_id'] ?? -1);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
