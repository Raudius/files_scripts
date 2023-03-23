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
 * @method setLimitGroups(string $groups)
 * @method int getLimitGroups()
 * @method setPublic(bool $public)
 * @method bool getPublic()
 * @method setRequestDirectory(int $enabled)
 * @method int getRequestDirectory()
 */
class Script extends Entity implements JsonSerializable {
	protected ?string $title = null;
	protected ?string $description = null;
	protected ?string $program = null;
	protected ?int $enabled = null;
	protected ?string $limitGroups = null;
	protected ?int $requestDirectory = null;
	protected ?bool $public = null;

	public function setLimitGroupsArray(array $groupsArray): void {
		$groups = implode(",", $groupsArray) ?: '';
		$this->setLimitGroups($groups);
	}

	public function getLimitGroupsArray(): array {
		return array_filter(explode(",", $this->limitGroups) ?: []);
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'program' => $this->program,
			'enabled' => $this->enabled,
			'limitGroups' => $this->getLimitGroupsArray(),
			'requestDirectory' => $this->requestDirectory,
			'public' => $this->public,
		];
	}
}
