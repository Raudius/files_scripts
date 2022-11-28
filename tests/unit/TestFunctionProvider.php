<?php
namespace OCA\FilesScripts;

use OCA\FilesScripts\Interpreter\Functions\Error\Abort;
use OCA\FilesScripts\Interpreter\Functions\Error\Log;
use OCA\FilesScripts\Interpreter\Functions\Files\Copy_File;
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
use OCA\FilesScripts\Interpreter\Functions\Files\Node_Exists;
use OCA\FilesScripts\Interpreter\Functions\Files\Root;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Input_Files;
use OCA\FilesScripts\Interpreter\Functions\Input\Get_Target_Folder;
use OCA\FilesScripts\Interpreter\Functions\Media\Ffmpeg;
use OCA\FilesScripts\Interpreter\Functions\Media\Ffprobe;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Comment_Create;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Comment_Delete;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Comments_Find;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Notify;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tag_Create;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tag_File;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tag_File_Unassign;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Tags_Find;
use OCA\FilesScripts\Interpreter\Functions\Nextcloud\Users_Find;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Decrypt;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Merge;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Overlay;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Page_Count;
use OCA\FilesScripts\Interpreter\Functions\Pdf\Pdf_Pages;
use OCA\FilesScripts\Interpreter\Functions\Template\Html_To_Pdf;
use OCA\FilesScripts\Interpreter\Functions\Template\Mustache;
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
use OCA\FilesScripts\Interpreter\IFunctionProvider;

class TestFunctionProvider implements IFunctionProvider {

	public function getFunctions(): array {
		$logger = new NullLogger();
		$tempManager = new TestTempManager();
		return [
			new Exists(),
			new Copy_File($logger),
			new New_File(),
			new Full_Path(),
			new Get_Parent(),
			new Root($logger),
			new Meta_Data(),
			new File_Content(),
			new Directory_Listing(),
			new Is_File(),
			new Is_Folder(),
			new Pdf_Merge(),
			new Pdf_Overlay(),
			new Pdf_Decrypt(),
			new Abort(),
			new Get_Input(),
			new Get_Input_Files(),
			new Get_Target_Folder(),
			new Html_To_Pdf($tempManager),
			new Mustache(),
			new Sort(),
			new Json(),
			new Http_Request(),
			new Pdf_Pages(),
			new File_Delete(),
			new Node_Exists(),
			new Pdf_Page_Count(),
			new Format_Price(),
			new Create_Date_Time(),
			new Format_Date_Time(),
			new File_Unlock(),
			// new File_Move(),
			new Wait(),
			new Log($logger),
			new For_Each(),
			new File_Copy(),
			new File_Copy_Unsafe(),
			new File_Move_Unsafe(),
			new Home(),
			// new Exists_Unsafe(),
			new Ffmpeg($logger, $tempManager),
			new Ffprobe(),
			new Csv_To_Table(),
			// new Tag_File(),
			// new Tag_File_Unassign(),
			// new Tags_Find(),
			// new Tag_Create(),
			// new Notify(),
			// new Users_Find(),
			new Shell_Command(),
			// new Comments_Find(),
			// new Comment_Delete(),
			// new Comment_Create()
		];
	}

	public function isRegistrable(): bool {
		return true;
	}
}
