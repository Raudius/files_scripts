<?php

namespace OCA\FilesScripts\Interpreter;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Event\RegisterScriptFunctionsEvent;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\ITempManager;
use raudius\phpdf\Phpdf;

class Interpreter {
	private ITempManager $tempManager;
	private IEventDispatcher $dispatcher;

	public function __construct(
		ITempManager $tempManager,
		IEventDispatcher $dispatcher
	) {
		$this->tempManager = $tempManager;
		$this->dispatcher = $dispatcher;
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
		$functionsEvent = new RegisterScriptFunctionsEvent();
		$this->dispatcher->dispatchTyped($functionsEvent);

		$functions = $functionsEvent->getFunctions();
		foreach ($functions as $function) {
			$function->register($context);
		}
	}
}
