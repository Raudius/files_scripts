<?php

namespace OCA\FilesScripts;

require_once __DIR__ . '/../vendor/autoload.php';

use OCA\FilesScripts\Interpreter\RegistrableFunction;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\ClassLoader\ClassMapGenerator;

const DOC_FILE = __DIR__ . '/../docs/Functions.md';
const FUNCTIONS_DIR = __DIR__ . '/../lib/Interpreter/Functions/';

const PATTERN_COMMENT_START_END = '/^(\/\*\*\n)|(\s*\*\/)/';
const PATTERN_COMMENT_LINE_DECOR = '/^( \* )|( \*(?=\n))/m';
const PATTERN_NEW_LINE = '/(\\n\\r)|(\\r\\n)|\n/';

const TYPE_DESCRIPTIONS = [
	'Files' => 'File operations within the Nextcloud environment',
	'Error' => 'Reporting and logging',
	'Input' => 'Retreiving user inputs',
	'Pdf' => 'Modify PDFs (requires qpdf server package)',
	'Template' => 'Generate files from templates',
	'Util' => 'Utility functions for scripting convenience'
];

$functionClasses = (array_keys(ClassMapGenerator::createMap(FUNCTIONS_DIR)));
$functionDocs = [];
foreach ($functionClasses as $functionClass) {
	$type = getFunctionType($functionClass);
	$doc = getClassDoc($functionClass);
	if (!is_a($functionClass,RegistrableFunction::class, true)) {
		echo "Skipped: " . $functionClass . PHP_EOL;
		continue;
	}

	$functionName = $functionClass::getFunctionName();
	$functionDocs[$type][$functionName] = <<<MD
### $functionName

$doc

MD;
}

$stream = fopen(DOC_FILE, "wb");
fwrite($stream, generateToC($functionDocs));
foreach ($functionDocs as $type => $function) {
	fwrite($stream, <<<MD
## $type

MD
);

	foreach ($function as $doc) {
		fwrite($stream, $doc);
	}
}
fclose($stream);


/*
 * Script helper functions
 */
function generateToC(array $groupedFunctions): string {
	$toc = '';
	foreach ($groupedFunctions as $type => $functions) {
		$toc .= "  - **[$type:](#$type)** " . (TYPE_DESCRIPTIONS[$type] ?? '') . "\n";
		foreach ($functions as $name => $_) {
			$toc .= "    - [$name](#$name)  \n";
		}
		$toc .= "\n";
	}

	return $toc;
}

function getClassDoc(string $class): string {
	try {
		$doc = (new ReflectionClass($class))->getDocComment();
		$doc = preg_replace(PATTERN_COMMENT_START_END, "", $doc);
		$doc = preg_replace(PATTERN_COMMENT_LINE_DECOR, "", $doc);
		$doc = preg_replace(PATTERN_NEW_LINE, "  \n", $doc);
		return $doc;
	} catch (ReflectionException $e) {
		return '';
	}
}

function getFunctionType(string $class): string {
	preg_match('/\\\Functions\\\(\w+)\\\\/', $class, $matches, PREG_UNMATCHED_AS_NULL);
	return $matches[1];
}
