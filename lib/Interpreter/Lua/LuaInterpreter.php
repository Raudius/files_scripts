<?php
namespace OCA\FilesScripts\Interpreter\Lua;

use Lua;

class LuaInterpreter implements LuaWrapper {
	private Lua $interpreter;

	public function __construct() {
		$this->interpreter = new Lua();
	}

	public function eval(string $program) {
		return $this->interpreter->eval($program);
	}

	public function assign(string $key, $value): void {
		$this->interpreter->assign($key, $value);
	}

	public function call(callable $closure, array $args=[]) {
		$this->interpreter->call($closure, $args);
	}

	public function registerCallback(string $name, callable $callback): void {
		$this->interpreter->registerCallback($name, $callback);
	}

	public function getGlobalVariable(string $name) {
		return $this->interpreter->eval("return $name") ?: null;
	}
}
