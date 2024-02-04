<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

/**
 * Defines application features from the specific context.
 *
 * Copied structure from `nextcloud/collectives` integration tests by Jonas
 */
class FeatureContext implements Context {
	private string $baseUrl;
	private string $remoteUrl;
	private string $ocsUrl;
	private array $clientOptions;
	private ?ResponseInterface $response = null;
	private ?array $json = null;
	private ?string $currentUser = null;
	private array $cookieJars = [];
	private array $requestTokens = [];
	private int $scriptCount = 0; // Used to create unique names for scripts
	private bool $debugMode = true;

	/**
	 * Initializes context.
	 * Every scenario gets its own context instance.
	 * You can also pass arbitrary arguments to the context constructor through behat.yml.
	 *
	 * @param string $baseUrl
	 * @param string $remoteUrl
	 * @param string $ocsUrl
	 */
	public function __construct(string $baseUrl, string $remoteUrl, string $ocsUrl) {
		$this->baseUrl = $baseUrl;
		$this->remoteUrl = $remoteUrl;
		$this->ocsUrl = $ocsUrl;
		$this->clientOptions = ['verify' => false];
	}

	/**
	 * @Given :user deletes all scripts
	 */
	public function deleteAllScripts(string $user) {
		$scripts = $this->getAllScripts();
		foreach ($scripts as $script) {
			$this->userDeletesScript($user, $script["title"]);
		}
	}

	/**
	 * @Given :user deletes all tags
	 */
	public function deleteAllTags(string $user) {
		$tagIds = $this->getAllTagIds();
		foreach ($tagIds as $tagId) {
			$this->sendRemoteRequest("DELETE", "/dav/systemtags/$tagId");
			$this->assertStatusCode(204);
		}
	}

	/**
	 * @When :user deletes script :scriptName
	 */
	public function userDeletesScript(string $user, string $scriptName) {
		$this->debug("User ($user) deletes script ($scriptName)");
		$scriptId = $this->getScriptIdByName($scriptName);
		Assert::assertNotNull($scriptId);

		$this->sendRequest("DELETE", "/apps/files_scripts/scripts/$scriptId");
		$this->assertStatusCode(200);
	}

	/**
	 * @When user :user creates script :scriptLuaFile
	 *
	 * @throws GuzzleException
	 */
	public function userCreatesScript(string $user, string $scriptLuaFile): void {
		$this->debug("User $user creates script from file $scriptLuaFile");
		$this->setCurrentUser($user);

		$scriptLua = $this->readFixture($scriptLuaFile);
		$this->sendRequest('POST', '/apps/files_scripts/scripts', $this->newScript($scriptLua));
		$this->assertStatusCode(200);
	}

	/**
	 * @Then script :scriptJsonFile is run with :outputFileOrNo output
	 */
	public function runScript(string $scriptJsonFile, ?string $outputFileOrNo = null): void {
		$this->debug("Create and run script as admin ($scriptJsonFile)");

		$this->userSetsAppSetting("admin", "php_interpreter", "true");
		$this->userCreatesScript("admin", $scriptJsonFile);
		$scriptId = $this->getScriptIdFromResponse();
		Assert::assertNotNull($scriptId);

		$this->userRunsScriptId("admin", $scriptId);
		$response = $this->getJson();

		$error = $response["error"] ?? null;
		$messages = $respone["messages"] ?? null;

		$hasOutput = $outputFileOrNo !== "no";
		if (!$hasOutput) {
			Assert::assertNull($error, "Script failed with error: " . $error);
			Assert::assertEmpty($messages, "not empty messages " . json_encode($messages));
		} else {
			$outputString = $this->readFixture($outputFileOrNo);
			$output = json_decode($outputString, true);

			Assert::assertEqualsCanonicalizing($output, $response);
		}
	}

	/**
	 * @Then user :user runs script
	 */
	public function userRunsScriptId(string $user, int $scriptId) {
		$this->debug("User ($user) runs script with ID: $scriptId");

		$this->setCurrentUser($user);
		$this->sendRequest('POST', "/apps/files_scripts/run/$scriptId");
	}

	/**
	 * @Then user :user sets app setting :settingName to :settingValue
	 *
	 * @param string $user
	 * @param string $settingName
	 * @param string $settingValue
	 * @return void
	 * @throws GuzzleException
	 */
	public function userSetsAppSetting(string $user, string $settingName, string $settingValue): void {
		$this->setCurrentUser($user);
		$this->sendRequest("POST", "/apps/files_scripts/settings", [
			"name" => $settingName,
			"value" => $settingValue
		]);
		$this->assertStatusCode(200);
	}


	/**
	 * @return array
	 * @throws JsonException
	 */
	private function getJson(): array {
		$response = $this->response->getBody()->getContents();
		try {
			if (!$this->json) {
				$this->json = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
			}
			return $this->json;
		} catch (\Exception $e) {
			Assert::fail("Failed to parse JSON response: $response");
		}
	}

	/**
	 * @throws GuzzleException
	 * @throws JsonException
	 */
	private function getAllScripts(): array {
		$this->setCurrentUser("admin");
		$this->sendRequest('GET', '/apps/files_scripts/scripts/all');

		return $this->getJson();
	}

	/**
	 * @throws GuzzleException
	 * @throws JsonException
	 */
	private function getAllTagIds(): array {
		$this->setCurrentUser("admin");
		$this->sendRemoteRequest('PROPFIND', '/dav/systemtags/', '<?xml version="1.0"?>
<d:propfind  xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
  <d:prop>
    <oc:id />
    <oc:display-name />
    <oc:user-visible />
    <oc:user-assignable />
    <oc:can-assign />
  </d:prop>
</d:propfind>');

		$davResponse = $this->response->getBody()->getContents();

		// Hacky, but beats parsing the entire webdav response
		$matches = [];
		preg_match_all(
			'/<oc:id>([^<]+)</',
			$davResponse,
			$matches,
			PREG_PATTERN_ORDER
		);

		$idStrings = $matches[1] ?? [];
		return array_map('intval', $idStrings);
	}

	/**
	 * @throws GuzzleException
	 * @throws JsonException
	 */
	private function getScriptIdByName(string $name): ?int {
		$this->getAllScripts();
		return $this->getScriptIdByNameFromResponse($name);
	}

	/**
	 * @throws GuzzleException
	 * @throws JsonException
	 */
	private function getScriptIdByNameFromResponse(string $name): ?int {
		$jsonBody = $this->getJson();
		foreach ($jsonBody as $script) {
			if ($name === $script['title']) {
				return is_int($script['id']) ? $script['id'] : null;
			}
		}
		return null;
	}

	/**
	 * @throws JsonException
	 */
	private function getScriptIdFromResponse(): ?int {
		$jsonBody = $this->getJson();

		$isScript = isset($jsonBody["id"], $jsonBody["title"], $jsonBody["description"], $jsonBody["program"], $jsonBody["enabled"]);
		$id = $jsonBody["id"];
		if ($isScript && is_int($id)) {
			return $id;
		}

		return null;
	}

	/**
	 * @param string         $verb
	 * @param string         $url
	 * @param string|array|null $body
	 * @param array          $headers
	 * @param bool|null      $auth
	 *
	 * @throws GuzzleException
	 */
	private function sendRequest(string $verb,
								 string $url,
								 $body = null,
								 array $headers = [],
								 ?bool $auth = true): void {
		$fullUrl = $this->baseUrl . $url;
		$this->sendRequestBase($verb, $fullUrl, $body, $headers, $auth);
	}

	/**
	 * @param string               $verb
	 * @param string               $url
	 * @param string|resource|null $body
	 * @param array                $headers
	 * @param bool|null            $auth
	 *
	 * @throws GuzzleException
	 */
	private function sendRemoteRequest(string $verb,
									   string $url,
											  $body = null,
									   array $headers = [],
									   ?bool $auth = true): void {
		$fullUrl = $this->remoteUrl . $url;
		$this->sendRequestBase($verb, $fullUrl, $body, $headers, $auth);
	}

	/**
	 * @param string         $verb
	 * @param string         $url
	 * @param TableNode|null $body
	 * @param array          $headers
	 * @param bool|null      $auth
	 *
	 * @throws GuzzleException
	 */
	private function sendOcsRequest(string $verb,
									string $url,
									?TableNode $body = null,
									array $headers = [],
									?bool $auth = true): void {
		$fullUrl = $this->ocsUrl . $url;

		// Add Xdebug trigger variable as GET parameter
		$ocsJsonFormat = 'format=json';
		if (false !== strpos($fullUrl, '?')) {
			$fullUrl .= '&' . $ocsJsonFormat;
		} else {
			$fullUrl .= '?' . $ocsJsonFormat;
		}
		$this->sendRequestBase($verb, $fullUrl, $body, $headers, $auth);
	}

	/**
	 * @param string                $verb
	 * @param string                $url
	 * @param TableNode|string|null $body
	 * @param array                 $headers
	 * @param bool|null             $auth
	 *
	 * @throws GuzzleException
	 */
	private function sendRequestBase(string $verb,
									 string $url,
											$body = null,
									 array $headers = [],
									 ?bool $auth = true): void {
		$client = new Client($this->clientOptions);

		if (true === $auth && !isset($this->cookieJars[$this->currentUser])) {
			$this->cookieJars[$this->currentUser] = new CookieJar();
		}

		// Get request token for user (required due to CSRF checks)
		if (true === $auth && !isset($this->requestTokens[$this->currentUser])) {
			$this->getUserRequestToken($this->currentUser);
		}

		$options = ['cookies' => $this->cookieJars[$this->currentUser]];

		$options['headers'] = array_merge($headers, [
			'requesttoken' => $this->requestTokens[$this->currentUser],
		]);

		if ($body instanceof TableNode) {
			$fd = $body->getRowsHash();
			$options['form_params'] = $fd;
		} elseif (is_array($body)) {
			$options['json'] = $body;
		} elseif (is_string($body)) {
			$options['body'] = $body;
		}

		// Add Xdebug trigger variable as GET parameter
		$xdebugSession = 'XDEBUG_SESSION=PHPSTORM';
		if (false !== strpos($url, '?')) {
			$url .= '&' . $xdebugSession;
		} else {
			$url .= '?' . $xdebugSession;
		}

		// clear the cached json response
		$this->json = null;
		try {
			if ($verb === 'PROPFIND') {
				$this->response = $client->request('PROPFIND', $url, $options);
			} else {
				$this->response = $client->{$verb}($url, $options);
			}
		} catch (ClientException $e) {
			$this->response = $e->getResponse();
		}
	}

	/**
	 * @param string $user
	 *
	 * @throws GuzzleException
	 */
	private function getUserRequestToken(string $user): void {
		$loginUrl = $this->baseUrl . '/login';

		if (!isset($this->requestTokens[$user])) {
			// Request a new session and extract CSRF token
			$client = new Client($this->clientOptions);
			$response = $client->get(
				$loginUrl,
				['cookies' => $this->cookieJars[$user]]
			);
			$requestToken = substr(preg_replace('/(.*)data-requesttoken="(.*)">(.*)/sm', '\2', $response->getBody()->getContents()), 0, 89);

			// Login and extract new token
			$client = new Client($this->clientOptions);
			$this->response = $client->post(
				$loginUrl,
				[
					'form_params' => [
						'user' => $user,
						'password' => $user,
						'requesttoken' => $requestToken,
					],
					'cookies' => $this->cookieJars[$user],
				]
			);
			$this->assertStatusCode(200);

			$this->requestTokens[$user] = substr(preg_replace('/(.*)data-requesttoken="(.*)">(.*)/sm', '\2', $this->response->getBody()->getContents()), 0, 89);
		}
	}

	/**
	 * @param string $user
	 */
	private function setCurrentUser(string $user): void {
		$this->currentUser = $user;
	}

	/**
	 * @param mixed    $statusCode
	 * @param string   $message
	 */
	private function assertStatusCode($statusCode, string $message = ''): void {
		if (is_int($statusCode)) {
			$message = $message ?: 'Status code ' . $this->response->getStatusCode() . ' is not expected ' . $statusCode . '.';
			Assert::assertEquals($statusCode, $this->response->getStatusCode(), $message);
		} elseif (is_array($statusCode)) {
			$message = $message ?: 'Status code ' . $this->response->getStatusCode() . ' is neither of ' . implode(', ', $statusCode) . '.';
			Assert::assertContains($this->response->getStatusCode(), $statusCode, $message);
		}
	}


	private function readFixture(string $filename): string {
		$filepath = __DIR__ . '/../fixtures/' . $filename;
		$this->debug("Opening fixture $filename");
		$fileContent = file_get_contents($filepath);
		Assert::assertNotFalse($fileContent);

		return $fileContent ?: "";
	}

	private function newScript(string $scriptLua): array {
		$baseScript = $this->readFixture("test_base.lua");

		return [
			"title" => "test script " . $this->scriptCount++,
			"program" => "$baseScript\n$scriptLua",
			"description" => "",
			"enabled" => true,
			"public" => false,
			"limitGroups" => [],
			"fileTypes" => [],
			"showInContext" => false,
		];
	}

	private function debug(string $message) {
		if ($this->debugMode) {
			echo $message . PHP_EOL;
		}
	}
}
