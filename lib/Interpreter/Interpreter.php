<?php

namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Db\Script;
use OCP\ITempManager;
use raudius\phpdf\Phpdf;

class Interpreter {
	private FunctionProvider $functionProvider;
	private ITempManager $tempManager;

	public function __construct(
		FunctionProvider $functionProvider,
		ITempManager $tempManager
	) {
		$this->functionProvider = $functionProvider;
		$this->tempManager = $tempManager;
	}

	/**
	 * @throws AbortException - Thrown by registrable functions during $lua->eval()
	 */
	public function execute(Script $script, Context $context): void {
		$this->registerFunctions($context);

		$oldVal = ignore_user_abort(true);

		Phpdf::setTempDirectory($this->tempManager->getTempBaseDir());
		$context->getLua()->eval($script->getProgram());

		ignore_user_abort($oldVal);
	}

	private function registerFunctions(Context $context) {
		$functions = $this->functionProvider->getFunctions();
		foreach ($functions as $function) {
			$function->register($context);
		}
	}
}
