<?php
namespace OCA\FilesScripts\Lua;


class TemplateTest extends LuaTestCase {
	public function testMustache() {
		$program = <<<LUA
local tpl = "value='{{ value }}'"
output = mustache(tpl, {value= 'hello world!'})
LUA;

		$lua = $this->runLua($program);
		$output = $lua->getGlobalVariable('output');

		$this->assertEquals(
			"value='hello world!'",
			$output
		);
	}
}
