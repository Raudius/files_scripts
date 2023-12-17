<?php

namespace OCA\FilesScripts\Interpreter\Functions\Template;


use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * ~~`html_to_pdf(String html, [Table config]={}, [Table position]={}): string|nil`~~
 *
 * This function has been removed. The dependency that makes this function work is excessively large, and it is unnecessary to package it with the `file_scripts` app.
 *
 * You can continue using the function by manually installing the [`files_scripts_deprecated`](https://github.com/Raudius/files_scripts_deprecated) app, which bundles all the removed functions.
 */
class Html_To_Pdf extends RegistrableFunction {

	public function run($html = '', $config = [], $pos = []): ?string {
		return null;
	}
}
