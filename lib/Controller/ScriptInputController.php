<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\DB\Exception;
use OCP\IRequest;

class ScriptInputController extends Controller {
	private ScriptMapper $scriptMapper;
	private ScriptInputMapper $scriptInputMapper;

	public function __construct(
		$appName, IRequest $request,
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper
	) {
		parent::__construct($appName, $request);
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): Response {
		return new JSONResponse([], Http::STATUS_NOT_IMPLEMENTED);
	}

	public function createAll(int $scriptId, $scriptInputs): Response {
		$script = $this->scriptMapper->find($scriptId);
		if (!$script) {
			return new JSONResponse(['error' => 'Unknown script ID.'], Http::STATUS_NOT_FOUND);
		}

		$this->scriptInputMapper->deleteByScriptId($scriptId);
		foreach ($scriptInputs as $scriptInputArr) {
			$scriptInput = new ScriptInput();
			$scriptInput->setName($scriptInputArr['name']);
			$scriptInput->setDescription($scriptInputArr['description']);
			$scriptInput->setScriptId($scriptId);

			$this->scriptInputMapper->insert($scriptInput);
		}

		return new JSONResponse();
	}

	public function create (string $name, string $description, int $scriptId): Response {
		$script = $this->scriptMapper->find($scriptId);
		if (!$script) {
			return new JSONResponse(['error' => 'Unknown script ID.'], Http::STATUS_NOT_FOUND);
		}
		$scriptInput = new ScriptInput();
		$scriptInput->setName($name);
		$scriptInput->setDescription($description);
		$scriptInput->setScriptId($scriptId);

		try {
			$this->scriptInputMapper->insert($scriptInput);
		} catch (Exception $e) {
			return new JSONResponse(['error' => 'An error occurred when creating the script argument'], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse($script);
	}

	/**
	 * @throws Exception
	 */
	public function destroy(int $id): Response {
		$scriptInput = $this->scriptInputMapper->find($id);
		if (!$scriptInput) {
			return new JSONResponse();
		}

		$this->scriptInputMapper->delete($scriptInput);
		return new JSONResponse($scriptInput);
	}
}
