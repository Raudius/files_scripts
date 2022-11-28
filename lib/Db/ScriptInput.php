<?php

namespace OCA\FilesScripts\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;
use Throwable;

/**
 * @method setName(string $name)
 * @method string getName()
 * @method setDescription(string $description)
 * @method string getDescription()
 * @method setScriptId(int $scriptId)
 * @method string getScriptId()
 * @method setOptions(string $options)
 * @method string getOptions()
 */
class ScriptInput extends Entity implements JsonSerializable {
	protected ?string $name = null;
	protected ?string $description = null;
	protected ?int $scriptId = null;
	protected ?string $options = null;

	protected $value = null;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'description' => $this->description,
			'scriptId' => $this->scriptId,
			'options' => $this->getScriptOptions()
		];
	}

	/**
	 * @param string|array $options
	 */
	public function setScriptOptions($options): void {
		if (is_array($options)) {
			try {
				$options = json_encode($options, JSON_THROW_ON_ERROR);
			} catch (Throwable $e) {
				$options = '';
			}
		}

		$this->setOptions($options);
	}

	public function getScriptOptions(): array {
		try {
			return json_decode($this->getOptions(), true, 3, JSON_THROW_ON_ERROR);
		} catch (Throwable $e) {
			return [];
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value): void {
		$this->value = $value;
	}
}
