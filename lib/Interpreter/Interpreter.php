<?php
namespace OCA\FilesScripts\Interpreter;

use Lua;
use OCA\FilesScripts\Interpreter\Functions\Error\Abort;
use OCA\FilesScripts\Interpreter\Functions\Files\Exists;
use OCA\FilesScripts\Interpreter\Functions\Files\Copy_File;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Content;
use OCA\FilesScripts\Interpreter\Functions\Files\Get_Parent;
use OCA\FilesScripts\Interpreter\Functions\Files\Full_Path;
use OCA\FilesScripts\Interpreter\Functions\Files\Meta_Data;
use OCA\FilesScripts\Interpreter\Functions\Files\New_File;
use OCA\FilesScripts\Interpreter\Functions\Files\Root;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input_Files;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Decrypt;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Merge;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Overlay;
use OCP\Files\Folder;
use OCP\Files\Node;

class Interpreter {
	private const REGISTRABLE_FUNCTIONS = [
		Exists::class,
		Copy_File::class,
		New_File::class,
		Full_Path::class,
		Get_Parent::class,
		Root::class,
		Meta_Data::class,
		File_Content::class,
		Pdf_Merge::class,
		Pdf_Overlay::class,
		Pdf_Decrypt::class,
		Abort::class,
	];

	/**
	 * @param string $program
	 * @param array $files
	 * @param Folder $root
	 * @return false|mixed
	 * @throws AbortException - Thrown by RegistrableFunctions
	 */
	public function execute(string $program, array $files, Folder $root) {

		$lua = $this->createLua($root, $files);
		$program = <<<LUA
__ = {}
$program
return __
LUA;

		return $lua->eval($program);
	}

	/**
	 * @param Folder $root
	 * @param Node[] $inputFiles
	 * @return Lua
	 */
	private function createLua(Folder $root, array $inputFiles): Lua {
		$lua = new Lua();

		foreach (self::REGISTRABLE_FUNCTIONS as $function) {
			(new $function($lua, $root))->register();
		}

		(new Get_Input_Files($lua, $root, $inputFiles))->register();

		return $lua;
	}
}
