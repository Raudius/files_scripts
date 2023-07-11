<?php

namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Interpreter\Functions\Output\Abort;
use OCA\FilesScripts\Interpreter\Functions\Output\Add_Message;
use OCA\FilesScripts\Interpreter\Functions\Output\Clear_Messages;
use OCA\FilesScripts\Interpreter\Functions\Output\Log;
use OCA\FilesScripts\Interpreter\Functions\Files\Directory_Listing;
use OCA\FilesScripts\Interpreter\Functions\Files\Exists;
use OCA\FilesScripts\Interpreter\Functions\Files\Exists_Unsafe;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Content;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Copy;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Copy_Unsafe;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Delete;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Move;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Move_Unsafe;
use OCA\FilesScripts\Interpreter\Functions\Files\File_Unlock;
use OCA\FilesScripts\Interpreter\Functions\Files\Full_Path;
use OCA\FilesScripts\Interpreter\Functions\Files\Get_Parent;
use OCA\FilesScripts\Interpreter\Functions\Files\Home;
use OCA\FilesScripts\Interpreter\Functions\Files\Is_File;
use OCA\FilesScripts\Interpreter\Functions\Files\Is_Folder;
use OCA\FilesScripts\Interpreter\Functions\Files\Meta_Data;
use OCA\FilesScripts\Interpreter\Functions\Files\New_File;
use OCA\FilesScripts\Interpreter\Functions\Files\New_Folder;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input_Files;
use OCA\FilesScripts\Interpreter\Functions\Media\Ffmpeg;
use OCA\FilesScripts\Interpreter\Functions\Media\Ffprobe;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Comment_Create;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Comment_Delete;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Comments_Find;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Get_File_Tags;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Shares_Find;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Share_File;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Share_Delete;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Notify;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tag_Create;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tag_File;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tag_File_Unassign;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tags_Find;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Users_Find;
use OCA\FilesScripts\Interpreter\Functions\Output\View_Files;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Decrypt;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Merge;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Overlay;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Page_Count;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Pages;
use OCA\FilesScripts\Interpreter\Functions\Template\Html_To_Pdf;
use OCA\FilesScripts\Interpreter\Functions\Template\Mustache;
use OCA\FilesScripts\Interpreter\Functions\Util\_Include;
use OCA\FilesScripts\Interpreter\Functions\Util\Create_Date_Time;
use OCA\FilesScripts\Interpreter\Functions\Util\Csv_To_Table;
use OCA\FilesScripts\Interpreter\Functions\Util\For_Each;
use OCA\FilesScripts\Interpreter\Functions\Util\Format_Date_Time;
use OCA\FilesScripts\Interpreter\Functions\Util\Format_Price;
use OCA\FilesScripts\Interpreter\Functions\Util\Http_Request;
use OCA\FilesScripts\Interpreter\Functions\Util\Json;
use OCA\FilesScripts\Interpreter\Functions\Util\Shell_Command;
use OCA\FilesScripts\Interpreter\Functions\Util\Sort;
use OCA\FilesScripts\Interpreter\Functions\Util\Wait;

class FunctionProvider implements IFunctionProvider {
	/** @var RegistrableFunction[] */
	private array $functions;

	public function __construct(
		Exists $f1,
		New_File $f3,
		Full_Path $f4,
		Get_Parent $f5,
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
		Html_To_Pdf $f19,
		Mustache $f20,
		Sort $f21,
		Json             $f22,
		Http_Request     $f23,
		Pdf_Pages        $f24,
		File_Delete      $f25,
		Pdf_Page_Count   $f27,
		Format_Price     $f28,
		Create_Date_Time $f29,
		Format_Date_Time $f30,
		File_Unlock      $f31,
		File_Move        $f32,
		Wait             $f33,
		Log              $f34,
		For_Each         $f35,
		File_Copy $f36,
		File_Copy_Unsafe $f37,
		File_Move_Unsafe $f38,
		Home $f39,
		Exists_Unsafe $f40,
		Ffmpeg $f41,
		Ffprobe $f42,
		Csv_To_Table $f43,
		Tag_File $f44,
		Tag_File_Unassign $f45,
		Tags_Find $f46,
		Tag_Create $f47,
		Notify $f48,
		Users_Find $f49,
		Shell_Command $f50,
		Comments_Find $f51,
		Comment_Delete $f52,
		Comment_Create $f53,
		Get_File_Tags $f54,
		New_Folder $f55,
		Add_Message $f56,
		Clear_Messages $f57,
		_Include $f58,
		Shares_Find $f59,
		Share_File $f60,
		Share_Delete $f62,
		View_Files $f63
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
