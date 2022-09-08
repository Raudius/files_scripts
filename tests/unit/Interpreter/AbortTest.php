<?php

namespace OCA\FilesScripts;

use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\Functions\Error\Abort;
use PHPUnit\Framework\TestCase;

class AbortTest extends TestCase {
	public function testAbort(): void {
		$this->expectException(AbortException::class);
		$this->expectExceptionMessage('testAbort');
		(new Abort())->run('testAbort');
	}
}
