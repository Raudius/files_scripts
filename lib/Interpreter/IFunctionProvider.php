<?php

namespace OCA\FilesScripts\Interpreter;

interface IFunctionProvider {
	/**
	 * @return RegistrableFunction[]
	 */
	public function getFunctions(): iterable;

	/**
	 * Some functions may rely on server dependencies or PHP extensions.
	 * This function returns whether the functions to be provided have their pre-requisites met.
	 */
	public function isRegistrable(): bool;
}
