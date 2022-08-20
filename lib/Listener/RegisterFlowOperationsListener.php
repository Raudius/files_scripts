<?php

namespace OCA\FilesScripts\Listener;

use OCA\FilesScripts\Flow\Operation;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;
use Psr\Container\ContainerInterface;

class RegisterFlowOperationsListener implements IEventListener {
	private ContainerInterface $container;

	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * @inheritDoc
	 */
	public function handle(Event $event): void {
		if (!$event instanceof RegisterOperationsEvent) {
			return;
		}
		$operation = $this->container->get(Operation::class);
		$event->registerOperation($operation);
		Util::addScript('files_scripts', 'files_scripts-workflow');
	}
}
