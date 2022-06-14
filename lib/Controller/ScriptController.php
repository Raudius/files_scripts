<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptMapper;
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
	private Interpreter $interpreter;
	private ScriptMapper $scriptMapper;
	private IRootFolder $rootFolder;

	public function __construct(
		$appName, IRequest $request,
		?string $userId,
		Interpreter $interpreter,
		ScriptMapper $scriptMapper,
		IRootFolder $rootFolder
	) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->interpreter = $interpreter;
		$this->scriptMapper = $scriptMapper;
		$this->rootFolder = $rootFolder;
	}

	/**
	 * @return DataResponse
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->scriptMapper->findAll());
	}

	public function create (string $title, string $description, string $program, bool $enabled): Response {
		$script = new Script();
		$script->setTitle($title);
		$script->setDescription($description);
		$script->setProgram($program);
		$script->setEnabled($enabled);
		try {
			$this->scriptMapper->insert($script);
		} catch (Exception $e) {
			return new JSONResponse([], Http::STATUS_NOT_MODIFIED);
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
	 * @NoCSRFRequired
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

		try {
			$this->scriptMapper->update($script);
		} catch (Exception $e) {
			return new JSONResponse([], Http::STATUS_NOT_MODIFIED);
		}
		return new JSONResponse();
	}

	/**
	 * @param int $id
	 * @param array $files
	 * @return Response
	 * @NoAdminRequired
	 * @NoCSRFRequired
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
			$this->interpreter->execute($script->getProgram(), $files, $this->userId);
		} catch (AbortException $e) {
			return new JSONResponse(['error' => $e->getMessage()], HTTP::STATUS_NOT_MODIFIED);
		}

		return new JSONResponse();
	}
}
