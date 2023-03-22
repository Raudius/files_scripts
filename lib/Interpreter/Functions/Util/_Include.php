<?php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `include(Node|string lua_file): Bool`
 *
 * Loads the given Lua file into the global environment. Can be used to load common functions and variables, effectively extending the scripting API.
 * Scripting API functions are loaded and available to be used inside the included file.
 *
 * ```lua
 * -- Load file from a file on the server OR load from a Node object
 * success = include("/var/www/private/my_api.lua")
 * success = include(get_input_files()[1])
 * if (not success) then
 *   abort("Failed to load required script")
 * end
 *
 * -- Globally defined functions in the included file are now available in this script :)
 * my_api_function()
 * ```
 */
class _Include extends RegistrableFunction {
	public function run($file = null): bool {
		if (!is_string($file) && !is_array($file)) {
			return false;
		}

		$lua = $this->getFileContent($file);
		if ($lua === null) {
			return false;
		}

		try {
			$this->getContext()->getLua()->eval($lua);
		} catch (\Exception|\Throwable $e) {
			return false;
		}

		return true;
	}

	/**
	 * Get the content from the file. File can be a path or a node array.
	 * @param array|string $file
	 * @return string|null
	 */
	private function getFileContent($file): ?string {
		if (is_string($file)) {
			return file_get_contents($file) ?: null;
		}

		try {
			$node = $this->getFile($this->getPath($file));
			if (!$node) {
				return null;
			}
			return $node->getContent();
		} catch (\Exception|\Throwable $exception) {
			return null;
		}
	}
}
