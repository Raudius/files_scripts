<?php

namespace OCA\FilesScripts\Interpreter\Functions\Files;

use Exception;
use OCA\Files_Trashbin\Events\MoveToTrashEvent;
use OCA\FilesScripts\Interpreter\RegistrableFunction;
use OCA\FilesScripts\Listener\MoveToTrashEventBypassListener;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Files\Node;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * `file_delete(Node node, [Bool success_if_not_found]=true, [Bool bypass_trahsbin]=true): Bool`
 *
 * Deletes the specified file/folder node.
 * Returns whether deletion succeeded.
 *
 * By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.
 *
 * The third argument `bypass_trashbin` may be used to delete the file permanently, if set to true.
 */
class File_Delete extends RegistrableFunction {

	private ContainerInterface $container;
	private LoggerInterface $logger;

	public function __construct(ContainerInterface $container, LoggerInterface $logger) {
		$this->container = $container;
		$this->logger = $logger;
	}

	public function run($node = [], $successIfNotFound = true, $bypassTrashbin = false): bool {
		$file = null;
		try {
			$file = $this->getNode($this->getPath($node));
		} catch (\Throwable $e) {
		}

		if (!$file) {
			return (bool) $successIfNotFound;
		}

		$bypassTrashbinFunc = $this->getBypassTrashbinListener($file);
		if ($bypassTrashbin) {
			$success = $this->registerBypassTrashbin($bypassTrashbinFunc);
			if (!$success) {
				$this->logger->error("files_scripts function `file_delete` failed to register the trash-bin bypass listener.");
				return false;
			}
		}

		try {
			$file->delete();
		} catch (Exception $e) {
			return false;
		} finally {
			$this->removeBypassTrashbinListener($bypassTrashbinFunc);
		}
		return true;
	}

	private function getBypassTrashbinListener(Node $node): callable {
		return function (Event $event) use ($node): void {
			if (!($event instanceof MoveToTrashEvent)) {
				return;
			}

			$expectedNodeId = $node->getId();
			$deletedNodeId = $event->getNode()->getId();
			if ($expectedNodeId !== null && $expectedNodeId === $deletedNodeId) {
				$event->disableTrashBin();
			}
		};
	}

	private function registerBypassTrashbin(callable $bypassTrashbinFunc): bool {
		/** @var IEventDispatcher|null $dispatcher */
		$dispatcher = $this->container->get(IEventDispatcher::class);
		if (!$dispatcher) {
			return false;
		}

		$dispatcher->addListener('OCA\Files_Trashbin::moveToTrash', $bypassTrashbinFunc);
		return true;
	}

	private function removeBypassTrashbinListener(callable $bypassTrashbinFunc): bool {
		/** @var IEventDispatcher|null $dispatcher */
		$dispatcher = $this->container->get(IEventDispatcher::class);
		if (!$dispatcher) {
			return false;
		}

		$dispatcher->removeListener(MoveToTrashEvent::class, $bypassTrashbinFunc);
		return true;
	}
}
