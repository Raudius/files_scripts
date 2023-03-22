<?php

namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Interpreter\Context;
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
use OCP\Files\IRootFolder;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class ScriptController extends Controller {
	private ?string $userId;
	private ScriptMapper $scriptMapper;
	private ScriptInputMapper $scriptInputMapper;
	private IRootFolder $rootFolder;
	private ScriptService $scriptService;
	private IL10N $l;
	private LoggerInterface $logger;
	private LuaProvider $luaProvider;
	private PermissionService $permissionService;

	public function __construct(
		$appName,
		IRequest $request,
		?string $userId,
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper,
		ScriptService $scriptService,
		IRootFolder $rootFolder,
		IL10N $l,
		LuaProvider $luaProvider,
		PermissionService $permissionService,
		LoggerInterface $logger
	) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
		$this->scriptService = $scriptService;
		$this->rootFolder = $rootFolder;
		$this->l = $l;
		$this->luaProvider = $luaProvider;
		$this->permissionService = $permissionService;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): Response {
		if (!$this->luaProvider->isAvailable()) {
			return new DataResponse([]);
		}

		$scripts = $this->scriptMapper->findAll();
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
		bool $requestDirectory
	): Response {
		$script = new Script();
		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);
		$script->setEnabled($enabled);
		$script->setLimitGroupsArray($limitGroups);
		$script->setRequestDirectory($requestDirectory);

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
		bool $requestDirectory
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
		$script->setRequestDirectory($requestDirectory);

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
	 * @NoAdminRequired
	 */
	public function run(int $id, string $outputDirectory = null, array $inputs = [], array $files = []): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script || !$this->permissionService->isEnabledForUser($script)) {
			return new JSONResponse(['error' => $this->l->t('Action does not exist or is disabled.')], Http::STATUS_NOT_FOUND);
		}

		$userFolder = $this->rootFolder->getUserFolder($this->userId);
		$fileNodes = [];
		$n = 1;
		foreach ($files as $file) {
			$path = $file['path'] . '/' . $file['name'];
			$fileNodes[$n++] = $userFolder->get($path);
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

		$context = new Context($this->luaProvider->createLua(), $userFolder, $scriptInputs, $fileNodes, $outputDirectory);
		try {
			$this->scriptService->runScript($script, $context);
		} catch (AbortException $e) {
			return new JSONResponse([
				'error' => $e->getMessage(),
				'messages' => $context->getMessages()
			], HTTP::STATUS_BAD_REQUEST);
		}

		return new JSONResponse(['messages' => $context->getMessages()]);
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
