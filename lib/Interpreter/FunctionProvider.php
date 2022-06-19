<?php
namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Interpreter\Functions\Error\Abort;
use OCA\FilesScripts\Interpreter\Functions\Files\Copy_File;
use OCA\FilesScripts\Interpreter\Functions\Files\Directory_Listing;
use OCA\FilesScripts\Interpreter\Functions\Files\Exists;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Content;
use OCA\FilesScripts\Interpreter\Functions\Files\Full_Path;
use OCA\FilesScripts\Interpreter\Functions\Files\Get_Parent;
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
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Pages;
use OCA\FilesScripts\Interpreter\Functions\Template\Html_To_Pdf;
use OCA\FilesScripts\Interpreter\Functions\Template\Mustache;
use OCA\FilesScripts\Interpreter\Functions\Util\Http_Request;
use OCA\FilesScripts\Interpreter\Functions\Util\Json;
use OCA\FilesScripts\Interpreter\Functions\Util\Sort_By;

class FunctionProvider {
	/** @var RegistrableFunction[] */
	private array $functions;

	public function __construct(
		Exists $f1,
		Copy_File $f2,
		New_File $f3,
		Full_Path $f4,
		Get_Parent $f5,
		Root $f6,
		Meta_Data $f7,
		File_Content $f8,
		Directory_Listing $f9,
		Is_File $f10,
		Is_Folder $f11,
		Pdf_Merge $f12,
		Pdf_Overlay $f13,
		Pdf_Decrypt $f14,
		Abort $f15,
		Get_Input $f16,
		Get_Input_Files $f17,
		Get_Target_Folder $f18,
		Html_To_Pdf $f19,
		Mustache $f20,
		Sort_By $f21,
		Json $f22,
		Http_Request $f23,
		Pdf_Pages $f24
	) {
		$this->functions = func_get_args();
	}

	public function getFunctions(): array {
		return $this->functions;
	}

	public function isRegistrable(): bool {
		return true;
	}
}
