<?php

namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Interpreter\ContextFactory;
use OCA\FilesScripts\Interpreter\Lua\LuaProvider;
use OCA\FilesScripts\Service\PermissionService;
use OCA\FilesScripts\Service\ScriptService;
use OCA\FilesScripts\Interpreter\AbortException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\DB\Exception;
use OCP\IL10N;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class ScriptController extends Controller {
	private ?string $userId;
	private ScriptMapper $scriptMapper;
	private ScriptInputMapper $scriptInputMapper;
	private ScriptService $scriptService;
	private IL10N $l;
	private LoggerInterface $logger;
	private LuaProvider $luaProvider;
	private PermissionService $permissionService;
	private ContextFactory $contextFactory;

	public function __construct(
		$appName,
		IRequest $request,
		?string $userId,
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper,
		ScriptService $scriptService,
		IL10N $l,
		LuaProvider $luaProvider,
		ContextFactory $contextFactory,
		PermissionService $permissionService,
		LoggerInterface $logger
	) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
		$this->scriptService = $scriptService;
		$this->l = $l;
		$this->contextFactory = $contextFactory;
		$this->luaProvider = $luaProvider;
		$this->permissionService = $permissionService;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function index(): Response {
		if (!$this->luaProvider->isAvailable()) {
			return new DataResponse([]);
		}

		$scripts = $this->scriptMapper->findAllStripProgram();
		$scripts = $this->permissionService->filterAllowedScripts($scripts);

		return new DataResponse(array_values($scripts));
	}

	/**
	 * Admin only, index all scripts regardless of permissions (e.g. to retrieve in settings page).
	 */
	public function adminIndex(): Response {
		return new DataResponse($this->scriptMapper->findAll());
	}

	public function create(
		string $title,
		string $description,
		string $program,
		bool $enabled,
		array $limitGroups,
		bool $public,
		?string $mimetype
	): Response {
		$script = new Script();
		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);
		$script->setLimitGroupsArray($limitGroups);
		$script->setPublic($public);
		$script->setMimetype($mimetype ?? "");

		$errors = $this->scriptService->validate($script);
		if ($errors) {
			return new JSONResponse(['error' => reset($errors)], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->scriptMapper->insert($script);
		} catch (Exception $e) {
			$this->logger->error('File scripts insert error', [
				'error_message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			return new JSONResponse(['error' => $this->l->t('An error occurred when saving the action.')], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse($script);
	}

	public function update(
		int $id,
		string $title,
		string $description,
		string $program,
		bool $enabled,
		array $limitGroups,
		bool $public,
		?string $mimetype
	): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);
		$script->setLimitGroupsArray($limitGroups);
		$script->setPublic($public);
		$script->setMimetype($mimetype ?? "");

		$errors = $this->scriptService->validate($script);
		if ($errors) {
			return new JSONResponse(['error' => reset($errors)], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->scriptMapper->update($script);
		} catch (Exception $e) {
			$this->logger->error('File scripts save error', [
				'error_message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);
			return new JSONResponse(['error' => $this->l->t('An error occurred when saving the action.')], Http::STATUS_BAD_REQUEST);
		}
		return new JSONResponse($script);
	}

	/**
	 * @PublicPage
	 * @NoAdminRequired
	 */
	public function run(int $id, array $inputs = [], array $files = [], string $shareToken = null): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script || !$this->permissionService->isEnabledForUser($script)) {
			return new JSONResponse(['error' => $this->l->t('Action does not exist or is disabled.')], Http::STATUS_NOT_FOUND);
		}

		if ($shareToken !== null && !$script->getPublic()) {
			return new JSONResponse(['error' => $this->l->t('This action is not enabled on public shares.')], Http::STATUS_FORBIDDEN);
		}

		$filePaths = [];
		foreach ($files as $file) {
			$filePaths[] = $file['path'] . '/' . $file['name'];
		}

		$groupedInputs = [];
		foreach ($inputs as $input) {
			$groupedInputs[$input['name']] = $input;
		}

		$scriptInputs = $this->scriptInputMapper->findAllByScriptId($id);
		foreach ($scriptInputs as $scriptInput) {
			$value = $groupedInputs[$scriptInput->getName()]['value'] ?? '';
			$scriptInput->setValue($value);
		}

		$context = null;
		if ($shareToken) {
			$context = $this->contextFactory->createContextForShare($shareToken, $scriptInputs, $filePaths);
		} elseif ($this->userId) {
			$context = $this->contextFactory->createContextForUser($this->userId, $scriptInputs, $filePaths);
		}

		if ($context === null) {
			$this->logger->error('Aborted attempt to run file action without a valid user session or share token.');
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		try {
			$this->scriptService->runScript($script, $context);
		} catch (AbortException $e) {
			return new JSONResponse([
				'error' => $e->getMessage(),
				'messages' => $context->getMessages()
			], HTTP::STATUS_BAD_REQUEST);
		}

		return new JSONResponse($context);
	}

	/**
	 * @throws Exception
	 */
	public function destroy(int $id): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$this->scriptMapper->delete($script);
		return new JSONResponse();
	}
}
