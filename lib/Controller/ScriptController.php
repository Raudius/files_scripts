<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Service\ScriptService;
use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\Interpreter;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\DB\Exception;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\IRequest;

class ScriptController extends Controller {
	private ?string $userId;
	private ScriptMapper $scriptMapper;
	private ScriptInputMapper $scriptInputMapper;
	private IRootFolder $rootFolder;
	private ScriptService $scriptService;

	public function __construct(
		$appName, IRequest $request,
		?string $userId,
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper,
		ScriptService $scriptService,
		IRootFolder $rootFolder
	) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
		$this->scriptService = $scriptService;
		$this->rootFolder = $rootFolder;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): Response {
		return new DataResponse($this->scriptMapper->findAll());
	}

	/**
	 * @NoAdminRequired
	 */
	public function getInputs($id): Response {
		return new DataResponse($this->scriptInputMapper->findAllByScriptId($id));
	}

	public function create (
		string $title,
		string $description,
		string $program,
		bool $enabled,
		bool $background,
		bool $requestDirectory
	): Response {
		$script = new Script();
		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);
		$script->setEnabled($enabled);
		$script->setBackground($background);
		$script->setRequestDirectory($requestDirectory);

		$errors = $this->scriptService->validate($script);
		if ($errors) {
			return new JSONResponse(['error' => reset($errors)], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->scriptMapper->insert($script);
		} catch (Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse($script);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(
		int $id,
		string $title,
		string $description,
		string $program,
		bool $enabled,
		bool $background,
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
		$script->setBackground($background);
		$script->setRequestDirectory($requestDirectory);

		$errors = $this->scriptService->validate($script);
		if ($errors) {
			return new JSONResponse(['error' => reset($errors)], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->scriptMapper->update($script);
		} catch (Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
		return new JSONResponse($script);
	}

	/**
	 * @NoAdminRequired
	 */
	public function run(int $id, string $outputDirectory = null, array $inputs = [], array $files = []): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script || !$script->getEnabled()) {
			return new JSONResponse(['error' => 'Script does not exist or is disabled.'], Http::STATUS_NOT_FOUND);
		}

		$userFolder = $this->rootFolder->getUserFolder($this->userId);
		$files = array_map(
			static function (array $fileData) use ($userFolder): Node {
				$path = $fileData['path'] . '/' . $fileData['name'];
				return $userFolder->get($path);
			},
			$files
		);

		$scriptInputs = [];
		foreach ($inputs as $input) {
			$scriptInputs[$input['name']] = $input['value'];
		}

		try {
			(new Interpreter($script, $userFolder))->execute($outputDirectory, $scriptInputs, $files);
		} catch (AbortException $e) {
			return new JSONResponse(['error' => $e->getMessage()], HTTP::STATUS_BAD_REQUEST);
		}

		return new JSONResponse();
	}

	/**
	 * @throws Exception
	 */
	public function destroy(int $id): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script) {
			return new JSONResponse(['error' => 'Script does not exist.'], Http::STATUS_NOT_FOUND);
		}

		$this->scriptMapper->delete($script);
		return new JSONResponse();
	}
}
