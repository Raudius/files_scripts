<?php
namespace OCA\FilesScripts\Lua;

use OC\Files\Node\Folder;
use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Interpreter\Context;
use OCA\FilesScripts\Interpreter\Interpreter;
use OCA\FilesScripts\Interpreter\Lua\LuarInterpreter;
use OCA\FilesScripts\TestFunctionProvider;
use OCA\FilesScripts\TestTempManager;
use PHPUnit\Framework\TestCase;

abstract class LuaTestCase extends TestCase {
	protected function runLua(string $program, array $inputs = []) {
		$mockFolder = $this->getMockBuilder(Folder::class)
			->disableOriginalConstructor()->getMock();
		$mockFolder->method('get')->willReturn($mockFolder);
		$mockFolder->method('getId')->willReturn('rootFolder');

		$lua = new LuarInterpreter();
		$context = new Context(
			$lua,
			$mockFolder,
			$inputs,
			[],
			null
		);

		$interpreter = new Interpreter(
			new TestFunctionProvider($this),
			new TestTempManager()
		);

		$script = new Script();
		$script->setProgram($program);

		$interpreter->execute($script, $context);
		return $lua;
	}
}
