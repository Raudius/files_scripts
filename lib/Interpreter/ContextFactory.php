<?php
namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Interpreter\Lua\LuaProvider;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Share\IManager;
use Psr\Log\LoggerInterface;

class ContextFactory {
	private IRootFolder $rootFolder;
	private LuaProvider $luaProvider;
	private IManager $shareManager;
	private LoggerInterface $logger;

	public function __construct(
		IRootFolder $rootFolder,
		LuaProvider $luaProvider,
		IManager $shareManager,
		LoggerInterface $logger
	) {
		$this->rootFolder = $rootFolder;
		$this->luaProvider = $luaProvider;
		$this->shareManager = $shareManager;
		$this->logger = $logger;
	}

	public function createContext(Folder $root, array $scriptInputs, array $filePaths): ?Context {
		$files = [];
		$n = 1;
		foreach ($filePaths as $filePath) {
			try {
				$file = $root->get($filePath);
			} catch (\Exception $e) {
				$this->logger->error('Could not find input file belonging in root folder for file action', [
					'root' => $root->getPath(),
					'path' => $filePath
				]);
				return null;
			}
			$files[$n++] = $file;
		}

		return new Context($this->luaProvider->createLua(), $root, $scriptInputs, $files);
	}

	public function createContextForShare(string $shareToken, array $scriptInputs, array $filePaths): ?Context {
		try {
			$share = $this->shareManager->getShareByToken($shareToken);
			$shareFolder = $share->getNode();

			if (!$shareFolder instanceof \OC\Files\Node\Folder) {
				$this->logger->error("Blocked attempt to run file action with share token for a file (folder expected).", [
					"shareToken" => $shareToken
				]);
				return null;
			}
		} catch (\Exception $e) {
			$this->logger->error("Failed to initialize share file action context.", [
				"exception_message" => $e->getMessage(),
				"exception_trace" => $e->getTraceAsString(),
				"shareToken" => $shareToken
			]);
			return null;
		}

		$context = $this->createContext($shareFolder, $scriptInputs, $filePaths);
		$context->setPermissionsOverride($share->getPermissions());

		return $context;
	}


	public function createContextForUser(string $userId, array $scriptInputs, $filePaths): ?Context {
		try {
			$userFolder = $this->rootFolder->getUserFolder($userId);
		} catch(\Exception $e) {
			$this->logger->error("Failed to initialize user file action context.", [
				"exception_message" => $e->getMessage(),
				"exception_trace" => $e->getTraceAsString(),
				"userId" => $userId
			]);
			return null;
		}

		return $this->createContext($userFolder, $scriptInputs, $filePaths);
	}
}
