<?php

namespace OCA\FilesScripts\Service;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptMapper;

class ScriptService {
	private ScriptMapper $scriptMapper;

	public function __construct(ScriptMapper $scriptMapper) {
		$this->scriptMapper = $scriptMapper;
	}

	/**
	 * Returns an array of validation errors.
	 *
	 * @return string[]
	 */
	public function validate(Script $script): array {
		$errors = [];

		$title = trim($script->getTitle());
		if (empty($title)) {
			$errors[] = 'Title is empty.';
		}

		$sameTitle = $this->scriptMapper->findByTitle($title);
		if ($sameTitle && $sameTitle->getId() !== $script->getId()) {
			$errors[] = 'A script already has this title.';
		}

		return $errors;
	}
}
