<?php
namespace OCA\FilesScripts\Lua;

use OCA\FilesScripts\Interpreter\AbortException;

class UtilTest extends LuaTestCase {

	public function testAbort() {
		$program = <<<LUA
abort('failed succesfully!')
LUA;

		$this->expectException(AbortException::class);
		$this->expectExceptionMessage('failed succesfully!');

		$this->runLua($program);
	}

	public function testForEach() {
		$program = <<<LUA
local input = { 1, 2, 3, 4, 5 }
output = "Output: "
for_each(input, function (i, v) 
  output = output .. v
end)
LUA;

		$lua = $this->runLua($program);
		$output = $lua->getGlobalVariable('output');
		$this->assertEquals('Output: 12345', $output);

	}

	public function testSort() {
		$program = <<<LUA
unsorted = {"banana", "apple", "cherry", "grapes", "kiwi", "orange", "melon", "watermelon", "mango"}
unsorted_keyed = {
	{name= "banana"}, 
	{name= "apple"}, 
	{name= "cherry"}, 
	{name= "grapes"}, 
	{name= "kiwi"},
	{name= "orange"}, 
	{name= "melon"}, 
	{name= "watermelon"}, 
	{name= "mango"}  
}

sorted = sort(unsorted)
sorted_desc = sort(unsorted, nil, false)

sorted_keyed = sort(unsorted_keyed, "name")
sorted_keyed_desc = sort(unsorted_keyed, "name", false)
LUA;

		$lua = $this->runLua($program);
		$items = $lua->getGlobalVariable('unsorted');
		sort($items);

		$sorted = $lua->getGlobalVariable('sorted');
		$sorted_desc = $lua->getGlobalVariable('sorted_desc');
		$sorted_keyed = $lua->getGlobalVariable('sorted_keyed');
		$sorted_keyed_desc = $lua->getGlobalVariable('sorted_keyed_desc');

		$count = count($items);
		foreach ($items as $i => $value) {
			$this->assertEquals($value, $sorted[$i+1]);
			$this->assertEquals($value, $sorted_keyed[$i+1]['name']);


			$this->assertEquals($value, $sorted_desc[$count-$i]);
			$this->assertEquals($value, $sorted_keyed_desc[$count-$i]['name']);
		}
	}
}
