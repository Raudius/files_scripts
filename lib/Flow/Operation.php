<?php

namespace OCA\FilesScripts\Flow;

use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\Context;
use OCA\FilesScripts\Interpreter\ContextFactory;
use OCA\FilesScripts\Interpreter\Lua\LuaProvider;
use OCA\FilesScripts\Service\ScriptService;
use OCA\WorkflowEngine\Entity\File;
use OCP\EventDispatcher\GenericEvent;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\SystemTag\MapperEvent;
use OCP\WorkflowEngine\IManager;
use OCP\WorkflowEngine\ISpecificOperation;
use Psr\Log\LoggerInterface;
use OCP\WorkflowEngine\IRuleMatcher;
use OCP\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent as LegacyGenericEvent;
use Throwable;
use UnexpectedValueException;

class Operation implements ISpecificOperation {
	private IURLGenerator $urlGenerator;
	private IL10N $l;
	private ScriptMapper $scriptMapper;
	private LoggerInterface $logger;
	private ScriptService $scriptService;
	private IUserSession $session;
	private IRootFolder $rootFolder;
	private LuaProvider $luaProvider;

	public function __construct(
		IURLGenerator $urlGenerator,
		IL10N $l,
		ScriptMapper $scriptMapper,
		ScriptService $scriptService,
		IRootFolder $rootFolder,
		IUserSession $session,
		LuaProvider $luaProvider,
		ContextFactory $contextFactory,
		LoggerInterface $logger
	) {
		$this->urlGenerator = $urlGenerator;
		$this->l = $l;
		$this->scriptMapper = $scriptMapper;
		$this->scriptService = $scriptService;
		$this->rootFolder = $rootFolder;
		$this->session = $session;
		$this->luaProvider = $luaProvider;
		$this->contextFactory = $contextFactory;
		$this->logger = $logger;
	}

	/**
	 * @throws UnexpectedValueException
	 * @since 9.1
	 */
	public function validateOperation(string $name, array $checks, string $scriptId): void {
		if (!$this->luaProvider->isAvailable()) {
			throw new UnexpectedValueException($this->l->t('Lua extension not installed on the server.'));
		}

		$script = $this->scriptMapper->find((int) $scriptId);
		if (!$script) {
			throw new UnexpectedValueException($this->l->t('No script was chosen.'));
		}
	}

	public function getDisplayName(): string {
		return $this->l->t('Run file action');
	}

	public function getDescription(): string {
		return $this->l->t('Pass files to a file action script and run it.');
	}

	public function getIcon(): string {
		return $this->urlGenerator->imagePath('files_scripts', 'files_scripts.svg');
	}

	public function isAvailableForScope(int $scope): bool {
		return $scope === IManager::SCOPE_ADMIN;
	}

	public function onEvent(string $eventName, Event $event, IRuleMatcher $ruleMatcher): void {
		if (!$event instanceof GenericEvent
			&& !$event instanceof LegacyGenericEvent
			&& !$event instanceof MapperEvent) {
			return;
		}

		$matches = $ruleMatcher->getFlows(false);

		foreach ($matches as $match) {
			$scriptId = $match['operation'] ?? -1;
			$script = $this->scriptMapper->find((int) $scriptId);
			$context = $this->createContext($eventName, $event);

			$eventData = [
				'script_id' => $scriptId,
				'flow_id' => $match['id'] ?? "",
				'match' => $match,
			];

			if (!$script) {
				$this->logger->info('Could not run the file action flow. File action possibly deleted', $eventData);
				continue;
			}

			if (!$context) {
				$this->logger->info('Could not run the file action flow. Could not get file context.', $eventData);
				continue;
			}

			try {
				$this->scriptService->runScript($script, $context);
			} catch (AbortException $e) {
				$this->logger->error('File action flow failed', ['error' => $e->getMessage(), 'info' => $eventData]);
			}
		}
	}

	public function getEntityId(): string {
		return File::class;
	}

	private function createContext(string $eventName, Event $event): ?Context {
		/**
		 * @var $newNodePath string|null
		 * @var $oldNodePath string|null
		 */
		[$newNodePath, $oldNodePath] = $this->getNodePaths($eventName, $event);

		$inputs = [
			ScriptInput::fromParams([
				'name' => 'old_node_path',
				'options' => json_encode(['type'=> 'text']),
				'value' => $oldNodePath // could be null
			])
		];
		$targetFiles = $newNodePath ? [$newNodePath] : [];

		try {
			$userId = $this->session->getUser()->getUID();
			return $this->contextFactory->createContextForUser($userId, $inputs, $targetFiles);
		} catch (Throwable $e) {
			$this->logger->info('Could not create context due to unexpected exception.', ['error_message' => $e->getMessage()]);
		}
		return null;
	}

	private function getNodePaths(string $eventName, Event $event): array {
		$user = $this->session->getUser();
		try {
			$rootFolder = $user ? $this->rootFolder->getUserFolder($user->getUID()) : $this->rootFolder;
		} catch (\Throwable $e) {
			$rootFolder = $this->rootFolder;
			$this->logger->error("Files scripts flow failed to get root folder for user", [
				'error_message' => $e->getMessage(),
				'error_trace' => $e->getTraceAsString(),
				'user_id' => $user ? $user->getUID() : null,
			]);
		}
		$newNode = null;
		$oldNode = null;

		if ($event instanceof GenericEvent) {
			if ($eventName === '\OCP\Files::postRename' || $eventName === '\OCP\Files::postCopy') {
				[$oldNode, $newNode] = $event->getSubject();
			} elseif ($eventName === '\OCP\Files::postDelete') {
				$oldNode = $event->getSubject();
			} else {
				$newNode = $event->getSubject();
			}
		}

		if ($event instanceof MapperEvent && $event->getObjectType() === 'files') {
			$nodes = $rootFolder->getById($event->getObjectId());
			if (isset($nodes[0])) {
				$newNode = $nodes[0];
			}
		}

		$newNode = $newNode instanceof Node ? $newNode : null;
		$oldNode = $oldNode instanceof Node ? $oldNode : null;

		$newNodePath = null;
		$oldNodePath = null;
		try {
			$newNodePath = $newNode ? $rootFolder->getRelativePath($newNode->getPath()) : null;
			$oldNodePath = $oldNode ? $rootFolder->getRelativePath($oldNode->getPath()) : null;
		}
		catch (Throwable $e) {
			$this->logger->error("Files scripts flow failed to get old/new file path for event", [
				'error_message' => $e->getMessage(),
				'error_trace' => $e->getTraceAsString(),
				'newNodePath' => $newNode ? $newNode->getPath() : '',
				'oldNodePath' => $oldNode ? $oldNode->getPath() : '',
			]);
		}

		return [$newNodePath, $oldNodePath];
	}
}
