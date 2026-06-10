<?php
namespace OCA\FilesScripts;

use OCP\ITempManager;

class TestTempManager implements ITempManager {
	public function getTemporaryFile(string $postFix = ''): string {
		return 'temp_file' . $postFix;
	}

	public function getTemporaryFolder(string $postFix = ''): string {
		return sys_get_temp_dir();
	}

	public function clean() {
	}

	public function cleanOld() {
	}

	public function getTempBaseDir() {
		return sys_get_temp_dir();
	}
}
