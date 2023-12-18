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
 * @method string getLimitGroups()
 * @method setPublic(int $public)
 * @method int getPublic()
 * @method setMimetype(string $mimetypes)
 * @method string getMimetype()
 * @method setFileTypes(?string $fileTypes)
 * @method string getFileTypes()
 * @method setShowInContext(int $showInContext)
 * @method int getShowInContext()
 */
class Script extends Entity implements JsonSerializable {
	protected ?string $title = null;
	protected ?string $description = null;
	protected ?string $program = null;
	protected ?int $enabled = null;
	protected ?string $limitGroups = null;
	protected ?int $public = null;
	protected ?string $fileTypes = null;
	protected ?int $showInContext = null;
	protected ?string $mimetype = null; // TODO remove mimetype property and drop column from db

	public function setLimitGroupsArray(array $groupsArray): void {
		$groups = implode(",", $groupsArray) ?: '';
		$this->setLimitGroups($groups);
	}

	public function getLimitGroupsArray(): array {
		return array_filter(explode(",", $this->limitGroups) ?: []);
	}
	public function setFileTypesArray(array $mimetypesArray): void {
		$mimetypes = implode(",", $mimetypesArray) ?: '';
		$this->setFileTypes($mimetypes);
	}

	public function getFileTypesArray(): array {
		if (!$this->fileTypes) {
			return [];
		}
		return array_filter(explode(",", $this->fileTypes) ?: []);
	}

	public static function newFromJson(array $jsonData): Script {
		$script = new Script();
		$script->setTitle($jsonData["title"] ?? "");
		$script->setDescription($jsonData["description"] ?? "");
		$script->setProgram($jsonData["program"] ?? "");

		// For backwards compatibility we allow the old `mimetype` type
		$fileTypes = $jsonData["fileTypes"] ?? [$jsonData["mimetype"]] ?: [];
		$script->setFileTypesArray($fileTypes);

		$enabled = $jsonData["enabled"] ?? 0;
		$enabled = is_integer($enabled) ? $enabled : 0;
		$script->setEnabled($enabled);

		$public = $jsonData["public"] ?? 0;
		if (!is_integer($public)) {
			$public = 0;
		}
		$script->setPublic($public);


		return $script;
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'program' => $this->program,
			'enabled' => $this->enabled,
			'showInContext' => $this->showInContext,
			'limitGroups' => $this->getLimitGroupsArray(),
			'public' => $this->public,
			'fileTypes' => $this->getFileTypesArray()
		];
	}
}
