<?php

namespace OCA\FilesScripts\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method setName(string $name)
 * @method string getName()
 * @method setDescription(string $description)
 * @method string getDescription()
 * @method setScriptId(int $scriptId)
 * @method string getScriptId()
 */
class ScriptInput extends Entity implements JsonSerializable {
	protected ?string $name = null;
	protected ?string $description = null;
	protected ?int $scriptId = null;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'description' => $this->description,
			'scriptId' => $this->scriptId,
		];
	}
}
