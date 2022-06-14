<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Interpreter\Interpreter;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\Response;
use OCP\DB\Exception;
use OCP\Files\File;
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

		return new JSONResponse([], Http::STATUS_OK);
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
		return new JSONResponse([], Http::STATUS_OK);
	}

	/**
	 * @return DataResponse
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $id): DataResponse {
		return new DataResponse([]);
		$lua = <<<LUA
file = {id = 69, path = "/a/b/", name = "test.md"}
folder = {id = 42, path = "/", name = "New folder"}
folder_ro = {id = 42, path = "/", name = "RO"}
foo = 'foo';

path = (full_path(get_parent(get_parent(get_parent(file)))) or '') ;
__['copy'] = copy_file(file, full_path(root()));
__['path'] = get_parent(get_parent(get_parent(get_parent(file))))
__['meta'] = meta_data(folder_ro)


pdf1 = { path = "/", name = "1.pdf" }
pdf2 = { path = "/", name = "2.pdf" }
pdf3 = { path = "/", name = "3.pdf" }
pdf4 = { path = "/", name = "4.pdf" }
pdf_pw = { path = "/", name = "password.pdf" }
__['merge'] = pdf_merge({pdf1, pdf2, pdf3, pdf4}, "merge.pdf")
__['overlay'] = pdf_overlay(pdf1, pdf4, "overlay.pdf")
__['decrypt'] = pdf_decrypt(pdf_pw, "12345", "decrypted.pdf")
LUA;

		return new DataResponse($this->interpreter->execute($lua, 'admin')); // TODO
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
		if (!$script) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$userFolder = $this->rootFolder->getUserFolder($this->userId);
		$files = array_map(
			static function (array $fileData) use ($userFolder): Node {
				$path = $fileData['path'] . '/' . $fileData['name'];
				return $userFolder->get($path);
			},
			$files
		);

		$this->interpreter->execute($script->getProgram(), $files, $this->userId);

		return new DataResponse([
			'foo' => 'bar',
			'method' => 'index',
			'user_id' => $this->userId,
			'id' => $script->id,
		]);
	}
}
