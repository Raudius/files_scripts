<?php

namespace OCA\FilesScripts\Interpreter\Functions\Template;

use Mustache_Engine;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `mustache(String template, [Table variables]={}): String`
 *
 * Renders a [Mustache](https://mustache.github.io) template.
 * Returns the resulting string.
 */
class Mustache extends RegistrableFunction {
	public function run($template = '', $vars = []): string {
		if (!$vars || !is_array($vars)) {
			$vars = [];
		}
		$vars = $this->normaliseArray($vars);

		return (new Mustache_Engine(['entity_flags' => ENT_QUOTES]))->render($template, $vars);
	}
}
