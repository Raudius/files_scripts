<?php
namespace OCA\FilesScripts\Interpreter;

use Lua;
use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Interpreter\Functions\Error\Abort;
use OCA\FilesScripts\Interpreter\Functions\Files\Directory_Listing;
use OCA\FilesScripts\Interpreter\Functions\Files\Exists;
use OCA\FilesScripts\Interpreter\Functions\Files\Copy_File;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Content;
use OCA\FilesScripts\Interpreter\Functions\Files\Get_Parent;
use OCA\FilesScripts\Interpreter\Functions\Files\Full_Path;
use OCA\FilesScripts\Interpreter\Functions\Files\Is_File;
use OCA\FilesScripts\Interpreter\Functions\Files\Is_Folder;
use OCA\FilesScripts\Interpreter\Functions\Files\Meta_Data;
use OCA\FilesScripts\Interpreter\Functions\Files\New_File;
use OCA\FilesScripts\Interpreter\Functions\Files\Root;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input_Files;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Target_Folder;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Decrypt;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Merge;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Overlay;
use OCP\Files\Folder;
use OCP\Files\NotFoundException;

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
		Directory_Listing::class,
		Is_File::class,
		Is_Folder::class,
		Pdf_Merge::class,
		Pdf_Overlay::class,
		Pdf_Decrypt::class,
		Abort::class,
	];

	private Folder $root;
	private Script $script;

	public function __construct(Script $script, Folder $root) {
		$this->script = $script;
		$this->root = $root;
	}

	/**
	 * @throws AbortException
	 */
	public function execute(?string $targetDirectory, array $input, array $files): void {
		$targetFolder = $this->getTargetDirectory($targetDirectory);

		$lua = $this->createLua();
		(new Get_Input_Files($lua, $this->root, $files))->register();
		(new Get_Input($lua, $this->root, $input))->register();
		$targetFolder && (new Get_Target_Folder($lua, $this->root, $targetFolder))->register();

		$oldVal = ignore_user_abort(true);

		$lua->eval($this->script->getProgram());

		ignore_user_abort($oldVal);
	}

	private function createLua(): Lua {
		$lua = new Lua();

		foreach (self::REGISTRABLE_FUNCTIONS as $function) {
			(new $function($lua, $this->root))->register();
		}

		return $lua;
	}

	/**
	 * @throws AbortException
	 */
	private function getTargetDirectory(?string $targetDirectory): ?Folder {
		if (!$this->script->getRequestDirectory()) {
			return null;
		}

		try {
			$folder = $this->root->get($targetDirectory);
			if ($folder instanceof Folder) {
				return $folder;
			}
		} catch (NotFoundException $e) {
		}

		throw new AbortException('Could not find the target directory.');
	}
}
