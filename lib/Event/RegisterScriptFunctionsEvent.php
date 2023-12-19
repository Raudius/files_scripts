<?php
namespace OCA\FilesScripts\Event;

use OCA\FilesScripts\Interpreter\IFunctionProvider;
use OCP\EventDispatcher\Event;

class RegisterScriptFunctionsEvent extends Event implements IFunctionProvider {
	/** @var IFunctionProvider[] */
	private array $functionProviders;

	public function __construct() {
		parent::__construct();
		$this->functionProviders = [];
	}

	public function registerFunctions(IFunctionProvider $functionProvider): void {
		$this->functionProviders[] = $functionProvider;
	}

	public function getFunctions(): iterable {
		foreach ($this->functionProviders as $functionProvider) {
			if (false === $functionProvider->isRegistrable()) {
				continue;
			}

			foreach ($functionProvider->getFunctions() as $function) {
				yield $function;
			}
		}
	}

	public function isRegistrable(): bool {
		return true;
	}
}
