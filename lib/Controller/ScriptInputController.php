<?php

namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Db\ScriptInputMapper;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Service\PermissionService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\DB\Exception;
use OCP\IL10N;
use OCP\IRequest;

class ScriptInputController extends Controller {
	private ScriptMapper $scriptMapper;
	private ScriptInputMapper $scriptInputMapper;
	private IL10N $l;
	private PermissionService $permissionService;

	public function __construct(
		$appName,
		IRequest $request,
		ScriptMapper $scriptMapper,
		ScriptInputMapper $scriptInputMapper,
		PermissionService $permissionService,
		IL10N $l
	) {
		parent::__construct($appName, $request);
		$this->scriptMapper = $scriptMapper;
		$this->scriptInputMapper = $scriptInputMapper;
		$this->permissionService = $permissionService;
		$this->l = $l;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): Response {
		return new JSONResponse([], Http::STATUS_NOT_IMPLEMENTED);
	}

	/**
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function getByScriptId($scriptId): Response {
		$script = $this->scriptMapper->find($scriptId);
		if (!$this->permissionService->isEnabledForUser($script) && !$this->permissionService->isUserAdmin()) {
			return new JSONResponse([], Http::STATUS_FORBIDDEN);
		}

		return new DataResponse($this->scriptInputMapper->findAllByScriptId($scriptId));
	}

	public function createAll(int $scriptId, $scriptInputs): Response {
		$script = $this->scriptMapper->find($scriptId);
		if (!$script) {
			return new JSONResponse(['error' => $this->l->t('Failed to create the action variables.')], Http::STATUS_NOT_FOUND);
		}

		$this->scriptInputMapper->deleteByScriptId($scriptId);
		foreach ($scriptInputs as $scriptInputArr) {
			$scriptInput = new ScriptInput();
			$scriptInput->setName($scriptInputArr['name']);
			$scriptInput->setDescription($scriptInputArr['description']);
			$scriptInput->setScriptOptions($scriptInputArr['options']);
			$scriptInput->setScriptId($scriptId);

			try {
				$this->scriptInputMapper->insert($scriptInput);
			} catch (Exception $e) {
				return new JSONResponse(['error' => $this->l->t('Failed to create the action variables.')], Http::STATUS_BAD_REQUEST);
			}
		}

		return new JSONResponse();
	}

	/**
	 * @throws Exception
	 */
	public function destroy(int $id): Response {
		$scriptInput = $this->scriptInputMapper->find($id);
		if (!$scriptInput) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$this->scriptInputMapper->delete($scriptInput);
		return new JSONResponse($scriptInput);
	}
}
