<?php

namespace OCA\FilesScripts\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method setTitle(string $title)
 * @method string getTitle()
 * @method setDescription(string $description)
 * @method string getDescription()
 * @method setProgram(string $program)
 * @method string getProgram()
 * @method setEnabled(int $enabled)
 * @method int getEnabled()
 * @method setBackground(int $enabled)
 * @method int getBackground()
 * @method setRequestDirectory(int $enabled)
 * @method int getRequestDirectory()
 */
class Script extends Entity implements JsonSerializable {
	protected ?string $title = null;
	protected ?string $description = null;
	protected ?string $program = null;
	protected ?int $enabled = null;
	protected ?int $background = null;
	protected ?int $requestDirectory = null;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'program' => $this->program,
			'enabled' => $this->enabled,
			'background' => $this->background,
			'requestDirectory' => $this->requestDirectory,
		];
	}
}
