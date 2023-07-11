<?php
namespace OCA\FilesScripts\Interpreter\Lua;

interface LuaWrapper {
	public function eval(string $program);

	public function assign(string $key, $value): void;

	function call(callable $closure, array $args=[]);

	public function registerCallback(string $name, callable $callback): void;

	public function getGlobalVariable(string $name);
}
