<?php
namespace OCA\FilesScripts\Interpreter\Functions\Template;

use Mustache_Engine;
use OCA\FilesScripts\Interpreter\RegistrableFunction;


/**
 *
 */
class Mustache extends RegistrableFunction {
	public function run(string $template = '', array $vars = []): string {
		return (new Mustache_Engine(array('entity_flags' => ENT_QUOTES)))->render($template, $vars);
	}
}
