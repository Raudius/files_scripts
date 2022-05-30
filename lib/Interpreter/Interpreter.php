<?php
namespace OCA\FilesScripts\Interpreter;

use Lua;
use OCA\FilesScripts\Interpreter\Functions\Files\Exists;
use OCA\FilesScripts\Interpreter\Functions\Files\Copy_File;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Content;
use OCA\FilesScripts\Interpreter\Functions\Files\Get_Parent;
use OCA\FilesScripts\Interpreter\Functions\Files\Full_Path;
use OCA\FilesScripts\Interpreter\Functions\Files\Meta_Data;
use OCA\FilesScripts\Interpreter\Functions\Files\Root;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Decrypt;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Merge;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Overlay;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\IUserManager;
use OCP\IUserSession;

class Interpreter {
	private const REGISTRABLE_FUNCTIONS = [
		Exists::class,
		Copy_File::class,
		Full_Path::class,
		Get_Parent::class,
		Root::class,
		Meta_Data::class,
		File_Content::class,
		Pdf_Merge::class,
		Pdf_Overlay::class,
		Pdf_Decrypt::class,
	];

	private IRootFolder $storage;
	private IUserManager $userManager;
	private IUserSession $userSession;

	public function __construct(
		IRootFolder $storage,
		IUserSession $userSession,
		IUserManager $userManager
	) {
		$this->storage = $storage;
		$this->userSession = $userSession;
		$this->userManager = $userManager;
	}

	public function execute(string $program, string $userId) {
		$user = $this->userManager->get($userId);
		if (!$user) {
			return null;
		}

		$userFolder = $this->storage->getUserFolder($user->getUID());
		$originalUser = $this->userSession->getUser();
		$this->userSession->setUser($user);

		$lua = $this->createLua($userFolder);
		$program = <<<LUA
__ = {}
$program
return __
LUA;

		$this->userSession->setUser($originalUser);

		return $lua->eval($program);
	}

	private function createLua(Folder $root): Lua {
		$lua = new Lua();

		foreach (self::REGISTRABLE_FUNCTIONS as $function) {
			(new $function($lua, $root))->register();
		}

		return $lua;
	}
}
