<?php

namespace OCA\FilesScripts\AppInfo;

return [
	'resources' => [
		'script' => ['url' => '/scripts'],
		'script_input' => ['url' => '/script_inputs'],
		'settings' => ['url' => '/settings']
	],
	'routes' => [
		['name' => 'script#run', 'url' => '/run/{id}', 'verb' => 'POST'],
		['name' => 'script#adminIndex', 'url' => '/scripts/all', 'verb' => 'GET'],
		['name' => 'script_input#getByScriptId', 'url' => '/script_inputs/{scriptId}', 'verb' => 'GET'],
		['name' => 'script_input#createAll', 'url' => '/script_inputs/{scriptId}', 'verb' => 'POST'],
		['name' => 'settings#modify', 'url' => '/settings', 'verb' => 'POST'],
	]
];
