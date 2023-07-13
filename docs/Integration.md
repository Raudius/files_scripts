# Integration with other Nextcloud apps

Other Nextcloud applications can register scripting API functions via the `RegisterScriptFunctionsEvent`.


```php
use OCA\FilesScripts\Interpreter\IFunctionProvider;

class RegisterScriptFunctionListener implements IEventListener {
	private FunctionProvider $functionProvider;

	public function __construct(FunctionProvider $functionProvider) {
		$this->functionProvider = $functionProvider;
	}

	public function handle(Event $event): void {
		if (!($event instanceof RegisterScriptFunctionsEvent)) {
			return;
		}

		$event->registerFunctions($this->functionProvider);
	}
}
```

As part of the event you should register some functions directly to the event. The functions are defined with a `RegistrableFunction` class and provided with a `IFunctionProvider` implementation. You can learn more about how to write registrable functions [here](Contribute_API.md).

Dont forget to register the listener! This is usually done as part of the `Application::register()` method.
```php
use OCA\FilesScripts\Event\RegisterScriptFunctionsEvent;

// ...
// class Application extends App implements IBootstrap
// ...
//	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(RegisterScriptFunctionsEvent::class, RegisterScriptFunctionListener::class);
//	}
```
