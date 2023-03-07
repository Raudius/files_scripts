<?php
namespace OCA\FilesScripts\Service;

use OCA\FilesScripts\Db\Script;
use OCA\FilesScripts\Db\ScriptMapper;
use OCA\FilesScripts\Interpreter\AbortException;
use OCA\FilesScripts\Interpreter\Context;
use OCA\FilesScripts\Interpreter\Interpreter;
use OCP\Files\Node;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;

class ScriptService {
	private ScriptMapper $scriptMapper;
	private IL10N $l;
	private Interpreter $interpreter;
	private LoggerInterface $logger;
	private IConfig $config;

	public function __construct(
		ScriptMapper $scriptMapper,
		Interpreter $interpreter,
		LoggerInterface $logger,
		IConfig $config,
		IL10N $l
	) {
		$this->scriptMapper = $scriptMapper;
		$this->interpreter = $interpreter;
		$this->logger = $logger;
		$this->config = $config;
		$this->l = $l;
	}

	/**
	 * @param Script $script
	 * @param Context $context
	 * @return void
	 * @throws AbortException
	 */
	public function runScript(Script $script, Context $context): void {
		try {
			$this->interpreter->execute($script, $context);
			return;
		} catch (AbortException $e) {
			throw $e;
		} catch (\Exception $e) {
			$this->logger->error('File scripts runtime error', [
				'error_message' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
				'script_id' => $script->getId(),
				'inputs' => $context->getInput(),
				'files' => array_map(
					static function (Node $node): string {
						return $node->getPath();
					}, $context->getInputFiles()
				)
			]);

			$error = $this->config->getSystemValue('debug', true)
				? $e->getMessage()
				: $this->l->t('An unexpected error occurred when running the action.');
			throw new AbortException($error);
		}
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
