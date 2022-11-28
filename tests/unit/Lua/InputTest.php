<?php
namespace OCA\FilesScripts\Lua;

use OCA\FilesScripts\Db\ScriptInput;

class InputTest extends LuaTestCase {

	public function testInputTypes() {
		$inputs = [
			ScriptInput::fromParams([
				'name' => 'text',
				'options' => json_encode(['type'=> 'text']),
				'value' => 'true'
			]),

			ScriptInput::fromParams([
				'name' => 'multiselect',
				'options' => json_encode(['type'=> 'text']),
				'value' => 'true'
			]),

			ScriptInput::fromParams([
				'name' => 'no_options',
				'value' => 1
			]),

			ScriptInput::fromParams([
				'name' => 'checkbox1',
				'options' => json_encode(['type'=> 'checkbox']),
				'value' => '1'
			]),

			ScriptInput::fromParams([
				'name' => 'checkbox2',
				'options' => json_encode(['type'=> 'checkbox']),
				'value' => '0'
			]),

			ScriptInput::fromParams([
				'name' => 'filepick',
				'options' => json_encode(['type'=> 'filepick']),
				'value' => '/test/'
			]),
		];

		$program = <<<LUA
checkbox1 = get_input('checkbox1')
checkbox2 = get_input('checkbox2')
text = get_input('text')
multiselect = get_input('multiselect')
no_options = get_input('no_options')
filepick = get_input('filepick')

unknown = get_input('unknown')

all_inputs = get_input()
LUA;


		$lua = $this->runLua($program, $inputs);

		// Check types are expected
		$this->assertIsBool($lua->getGlobalVariable('checkbox1'));
		$this->assertIsBool($lua->getGlobalVariable('checkbox2'));
		$this->assertIsString($lua->getGlobalVariable('text'));
		$this->assertIsString($lua->getGlobalVariable('multiselect'));
		$this->assertIsString($lua->getGlobalVariable('no_options'));

		// Check filepick gets converted to a node type
		$filepick = $lua->getGlobalVariable('filepick');
		$this->assertIsArray($filepick);
		$this->assertEquals('file', $filepick['_type']);

		// Unknown inputs should return null
		$this->assertNull($lua->getGlobalVariable('unknown'));


		// Check that get_inputs() returns array of all inputs
		$allInputs = $lua->getGlobalVariable('all_inputs');
		$this->assertIsArray($allInputs);
		foreach ($inputs as $input) {
			$this->assertArrayHasKey($input->getName(), $allInputs);
		}
	}
}
