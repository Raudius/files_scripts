<?php
namespace OCA\FilesScripts\Interpreter;

use Lua;
use OCA\FilesScripts\Db\Script;

class Interpreter {
	private FunctionProvider $functionProvider;

	public function __construct(FunctionProvider $functionProvider) {
		$this->functionProvider = $functionProvider;
	}

	/**
	 * @throws AbortException - Thrown by registrable functions during $lua->eval()
	 */
	public function execute(Script $script, Context $context): void {
		$lua = $this->createLua($context);

		$oldVal = ignore_user_abort(true);

		$lua->eval($script->getProgram());

		ignore_user_abort($oldVal);
	}

	private function createLua(Context $context): Lua {
		$lua = new Lua();

		$functions = $this->functionProvider->getFunctions();
		foreach ($functions as $function) {
			$function->register($lua, $context);
		}

		return $lua;
	}
}
