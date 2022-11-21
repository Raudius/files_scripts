<?php
namespace OCA\FilesScripts\Lua;

use OC\Files\Node\Folder;
use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Interpreter\Context;
use OCA\FilesScripts\Interpreter\Interpreter;
use OCA\FilesScripts\Interpreter\Lua\LuarInterpreter;
use OCA\FilesScripts\TestFunctionProvider;
use OCA\FilesScripts\TestTempManager;
use OCP\Files\SimpleFS\ISimpleFolder;
use PHPUnit\Framework\TestCase;

abstract class LuaTestCase extends TestCase {
	protected function runLua(string $program) {
		$folder = $this->collectiveFolder = $this->getMockBuilder(Folder::class)
			->disableOriginalConstructor()
			->getMock();

		$lua = new LuarInterpreter();
		$context = new Context(
			$lua,
			$folder,
			[],
			[],
			null
		);

		$interpreter = new Interpreter(
			new TestFunctionProvider(),
			new TestTempManager()
		);

		$script = new Script();
		$script->setProgram($program);

		$interpreter->execute($script, $context);
		return $lua;
	}
}
