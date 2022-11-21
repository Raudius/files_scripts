<?php

namespace OCA\FilesScripts\AppInfo;

return [
	'resources' => [
		'script' => ['url' => '/scripts'],
		'script_input' => ['url' => '/script_inputs']
	],
	'routes' => [
		['name' => 'script#run', 'url' => '/run/{id}', 'verb' => 'POST'],
		['name' => 'script#getInputs', 'url' => '/scripts/{id}/inputs', 'verb' => 'GET'],
		['name' => 'script_input#createAll', 'url' => '/script_inputs/{scriptId}', 'verb' => 'POST'],
	]
];
