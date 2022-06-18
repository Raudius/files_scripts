<?php
namespace OCA\FilesScripts\Interpreter\Functions\Template;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCP\ITempManager;


/**
 *
 */
class Html_To_Pdf extends RegistrableFunction {

	private ITempManager $tempManager;

	public function __construct(ITempManager $templateManager) {
		$this->tempManager = $templateManager;
	}

	public function run(string $html = ''): ?string {
		try {
			$mpdf = new Mpdf(['tempDir' => $this->tempManager->getTemporaryFolder()]);
			$mpdf->WriteHTML($html);
			return $mpdf->Output('', Destination::STRING_RETURN);
		} catch (MpdfException $e) {
			return null;
		}
	}
}
