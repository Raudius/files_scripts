<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCA\FilesScripts\Interpreter\Functions\Files\NodeSerializerTrait;
use OCP\Files\Folder;
use OCP\Share\IManager;
use OCP\Share\IShare;

/**
 * Trait which manages Share (de)serialization..
 */
trait ShareSerializerTrait {
	use NodeSerializerTrait;
	private function serializeShare(IShare $share, Folder $homeFolder): array {
		$shareData = [
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

		try {
			$node = $this->serializeNode($share->getNode(), $homeFolder);
			$shareData['node'] = $node;
		} catch (\Throwable $e) {
		}

		return $shareData;
	}

	private function deserializeShare(array $shareData, IManager $shareManager): ?IShare {
		try {
			return $shareManager->getShareById($shareData['full_id'] ?? -1);
		} catch (\Throwable $e) {
			return null;
		}
	}
}
