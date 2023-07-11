<?php
namespace OCA\FilesScripts\Interpreter\Lua;

use Raudius\Luar\Luar;

class LuarInterpreter implements LuaWrapper {
	private Luar $interpreter;

	public function __construct() {
		$this->interpreter = new Luar();
	}

	public function eval(string $program) {
		return $this->interpreter->eval($program);
	}

	public function assign(string $key, $value): void {
		$this->interpreter->assign($key, $value);
	}

	public function call(callable $closure, array $args=[]) {
		return $this->interpreter->callLuarClosure($closure, $args);
	}

	public function registerCallback(string $name, callable $callback): void {
		$this->interpreter->assign($name, $callback);
	}

	public function getGlobalVariable(string $name) {
		return $this->interpreter->getGlobals()[$name] ?? null;
	}
}
