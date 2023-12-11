<?php

namespace OCA\FilesScripts\Service;

class ScriptValidationException extends \Exception {
	private array $validationErrors;

	/**
	 * @param string[] $validations
	 */
	public function __construct(array $validationErrors) {
		$this->validationErrors = $validationErrors;
		$reasons = implode(',', $validationErrors);
		parent::__construct("Script is not valid: $reasons");
	}

	/**
	 * @return string[]
	 */
	public function getValidationErrors(): array {
		return $this->validationErrors;
	}
}
