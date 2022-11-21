<?php
namespace OCA\FilesScripts;

use OCP\ITempManager;

class TestTempManager implements ITempManager {
	public function getTemporaryFile($postFix = '') {
		return 'temp_file' . $postFix;
	}

	public function getTemporaryFolder($postFix = '') {
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
