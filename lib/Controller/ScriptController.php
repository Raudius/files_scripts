<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\Script;
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
	private IRootFolder $rootFolder;
	private ScriptService $scriptService;

	public function __construct(
		$appName, IRequest $request,
		?string $userId,
		ScriptMapper $scriptMapper,
		ScriptService $scriptService,
		IRootFolder $rootFolder
	) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->scriptMapper = $scriptMapper;
		$this->scriptService = $scriptService;
		$this->rootFolder = $rootFolder;
	}

	/**
	 * @return DataResponse
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->scriptMapper->findAll());
	}

	/**
	 * @param string $title
	 * @param string $description
	 * @param string $program
	 * @param bool $enabled
	 * @return Response
	 */
	public function create (string $title, string $description, string $program, bool $enabled): Response {
		$script = new Script();
		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);

		$errors = $this->scriptService->validate($script);
		if ($errors) {
			return new JSONResponse(['error' => reset($errors)], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->scriptMapper->insert($script);
		} catch (Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse();
	}

	/**
	 * @param int $id
	 * @param string $title
	 * @param string $description
	 * @param string $program
	 * @param bool $enabled
	 * @return Response
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $description, string $program, bool $enabled): Response {
		$script = $this->scriptMapper->find($id);
		if (!$script) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);

		$errors = $this->scriptService->validate($script);
		if ($errors) {
			return new JSONResponse(['error' => reset($errors)], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->scriptMapper->update($script);
		} catch (Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
		return new JSONResponse();
	}

	/**
	 * @param int $id
	 * @param array $files
	 * @return Response
	 * @NoAdminRequired
	 */
	public function run(int $id, array $files = []): Response {
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

		try {
			(new Interpreter())->execute($script->getProgram(), $files, $userFolder);
		} catch (AbortException $e) {
			return new JSONResponse(['error' => $e->getMessage()], HTTP::STATUS_BAD_REQUEST);
		}

		return new JSONResponse();
	}

	/**
	 * @param int $id
	 * @return Response
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
