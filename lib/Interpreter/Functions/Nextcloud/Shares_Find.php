<?php

namespace OCA\FilesScripts\Interpreter\Functions\Nextcloud;

use OCP\Constants;
use OCP\IUserSession;
use OCP\Share\IManager;
use OCP\Share\IShare;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `shares_find(Node|nil node=nil, Int[] share_types): Share[]`
 *
 * Finds shares created by, or shared with, the current user. If a node is given it finds shares for that Node. It is also possible to specify
 * which [share types](#share-types) to search for. If `share_types` is omitted all types will be searched.
 *
 * A list of share objects are returned, share objects are Lua tables which contain the following keys:
 *  - `_type`: used to identify the type of the object, always equal to `"share"`
 *  - `id`: the uid of the share
 *  - `full_id`: the full identifier reported by Nextcloud
 *  - `node`: the node object of the shared file/folder
 *  - `type`: the [type](#share-types) of share
 *  - `share_owner`: the ID of the user who created the shared file
 *  - `shared_by`:  the ID of the user who created the share
 *  - `shared_with`: the ID of the user who received the share
 *  - `permissions`: the [permissions](#Permissions) of the file
 *  - `token`: the token of the share (used for link shares in the URL `/index.php/s/<share-token>`)
 *
 * #### Share types
 * Nextcloud shares can have different types which offer different functionality, here is a list of constants provided in the API:
 *  - `SHARE_TYPE_USER`: file shared with a Nextcloud user
 *  - `SHARE_TYPE_GROUP`: file shared with a Nextcloud group
 *  - `SHARE_TYPE_LINK`: file shared via a public link
 *  - `SHARE_TYPE_REMOTE`: file shared to a federated Nextcloud instance
 *  - `SHARE_TYPE_EMAIL`: file shared via email
 *  - `SHARE_TYPE_ROOM`: file shared to a Talk room
 *  - `SHARE_TYPE_CIRCLE`: file shared with a Nextcloud circle
 *  - `SHARE_TYPE_DECK`: file attached to a Deck card
 *
 * #### Permissions
 * When sharing a file with a user you may select what the user can do with the file, these constants can be used to check/control these permissions, constants may be checked and combined with bitwise operations:
 *  - `PERMISSION_ALL`: All possible permissions, this option is equal to the bitwise-or of all other permissions
 *  - `PERMISSION_READ`: User is allowed to view the file(s)
 *  - `PERMISSION_CREATE`: User is able to create files within the shared location
 *  - `PERMISSION_DELETE`: User is able to delete the file(s)
 *  - `PERMISSION_UPDATE`: User is allowed to modify the file(s)
 *  - `PERMISSION_SHARE`: User is allowed to further share the file(s)
 */
class Shares_Find extends RegistrableFunction {
	use ShareSerializerTrait;

	const ALL_SHARE_TYPES = [
		IShare::TYPE_USER,
		IShare::TYPE_GROUP,
		IShare::TYPE_LINK,
		IShare::TYPE_REMOTE,
		IShare::TYPE_EMAIL,
		IShare::TYPE_ROOM,
		IShare::TYPE_CIRCLE,
		IShare::TYPE_DECK,
		IShare::TYPE_SCIENCEMESH,
	];

	private IManager $shareManager;
	private IUserSession $userSession;

	public function __construct(IUserSession $userSession, IManager $shareManager) {
		$this->userSession = $userSession;
		$this->shareManager = $shareManager;
	}

	public function run($nodeData=null, $shareTypes=null): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}

		$node = is_array($nodeData) ? $this->getNode($this->getPath($nodeData)) : null;
		$shareTypes = (is_array($shareTypes) && count($shareTypes) > 0) ? $shareTypes : self::ALL_SHARE_TYPES;
		$shareObjects = [];
		$homeFolder = $this->getHomeFolder();
		foreach ($shareTypes as $shareType) {
			try {
				$shares = $this->shareManager->getSharesBy($user->getUID(), $shareType, $node, false, -1, 0);
				foreach ($shares as $share) {
					$shareObjects[] = $this->serializeShare($share, $homeFolder);
				}
			} catch(\Throwable $e) {
				continue;
			}

			try {
				$shares = $this->shareManager->getSharedWith($user->getUID(), $shareType, $node,  -1, 0);
				foreach ($shares as $share) {
					$shareObjects[] = $this->serializeShare($share, $homeFolder);
				}
			} catch(\Throwable $e) {
				continue;
			}
		}
		return $shareObjects;
	}

	public function getConstants(): array {
		return [
			// Share types
			'SHARE_TYPE_USER' => IShare::TYPE_USER,
			'SHARE_TYPE_GROUP' => IShare::TYPE_GROUP,
			'SHARE_TYPE_LINK' => IShare::TYPE_LINK,
			'SHARE_TYPE_REMOTE' => IShare::TYPE_REMOTE,
			'SHARE_TYPE_EMAIL' => IShare::TYPE_EMAIL,
			'SHARE_TYPE_ROOM' => IShare::TYPE_ROOM,
			'SHARE_TYPE_CIRCLE' => IShare::TYPE_CIRCLE,
			'SHARE_TYPE_DECK' => IShare::TYPE_DECK,

			// File permissions
			'PERMISSION_ALL' => Constants::PERMISSION_ALL,
			'PERMISSION_READ' => Constants::PERMISSION_READ,
			'PERMISSION_CREATE' => Constants::PERMISSION_CREATE,
			'PERMISSION_DELETE' => Constants::PERMISSION_DELETE,
			'PERMISSION_UPDATE' => Constants::PERMISSION_UPDATE,
			'PERMISSION_SHARE' => Constants::PERMISSION_SHARE,
		];
	}
}
