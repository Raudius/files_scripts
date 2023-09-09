<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use ReflectionClass;

trait UnsafeViewOperationTrait {
	/**
	 * Hack alert!
	 * This hack was introduced, following nextcloud/server commit 55d943fd4b35c9df3e3639d6f264e4f8d8df82f0
	 *
	 * In this commit the defaultInstance is initialised to the user's home path instead of the server root.
	 *
	 * In order to trick Nextcloud into moving files to another user's home directory we need to set the defaultInstance
	 * of the Filesystem to the root view (path: `/`). This will be used during the `node->move()` method to evaluate the
	 * relative paths of the two files.
	 *
	 * @see \OC\Files\View::rename
	 * @see \OC\Files\View::getHookPath
	 */
	protected function tryUnsafeOperation(callable $operation) {
		$defaultInstance = null;

		try {
			$class = \OC\Files\Filesystem::class;
			$reflection = new ReflectionClass($class);

			$property = $reflection->getProperty("defaultInstance");
			$property->setAccessible(true);

			$defaultInstance = $reflection->getStaticPropertyValue("defaultInstance");
			$reflection->setStaticPropertyValue('defaultInstance', new \OC\Files\View('/'));

			return $operation();
		} catch (\Exception|\Throwable $e) {
			return null;
		} finally {
			// Set the defaultInstance back to the previous value.
			isset($reflection) && $reflection->setStaticPropertyValue('defaultInstance', $defaultInstance);
		}
	}
}
