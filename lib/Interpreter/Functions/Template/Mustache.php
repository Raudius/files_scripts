<?php

namespace OCA\FilesScripts\Interpreter\Functions\Template;

use Mustache_Engine;
use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `mustache(String template, [Table variables]={}): String`
 *
 * Renders a [mustache](https://mustache.github.io) template.
 * Returns the resulting string.
 */
class Mustache extends RegistrableFunction {
	public function run($template = '', $vars = [], $escape = false): string {
		if (!$vars || !is_array($vars)) {
			$vars = [];
		}
		$vars = $this->normaliseArray($vars);

		$options = $escape ? [
			'entity_flags' => ENT_QUOTES
		] : [
			'escape' => function($value) { return $value; }
		];

		return (new Mustache_Engine($options))->render($template, $vars);

	}
}
