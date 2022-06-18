<?php

namespace OCA\FilesScripts\Service;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptInput;
use OCA\FilesScripts\Db\ScriptMapper;
use OCP\IL10N;

class ScriptService {
	private ScriptMapper $scriptMapper;
	private IL10N $l;

	public function __construct(ScriptMapper $scriptMapper, IL10N $l) {
		$this->scriptMapper = $scriptMapper;
		$this->l = $l;
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
			$errors[] = $this->l->t('Title is empty.');
		}

		$sameTitle = $this->scriptMapper->findByTitle($title);
		if ($sameTitle && $sameTitle->getId() !== $script->getId()) {
			$errors[] = $this->l->t('A script already exists with this title.');
		}

		return $errors;
	}
}
