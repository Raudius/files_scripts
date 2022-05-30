<?php
namespace OCA\FilesScripts\Controller;

use OCA\FilesScripts\Interpreter\Interpreter;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ScriptController extends Controller {
	private ?string $userId;
	private Interpreter $interpreter;

	public function __construct(
		$appName, IRequest $request,
		?string $userId,
		Interpreter $interpreter
	) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->interpreter = $interpreter;
	}

	/**
	 * @return DataResponse
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): DataResponse {
		return new DataResponse(['foo' => 'bar', 'method' => 'index', 'user_id' => $this->userId]);
	}

	/**
	 * @return DataResponse
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $id): DataResponse {
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
	 * @return DataResponse
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function run(int $id, string $files = ''): DataResponse {
		return new DataResponse([
			'foo' => 'bar',
			'method' => 'index',
			'user_id' => $this->userId,
			'id' => $id
		]);
	}
}
