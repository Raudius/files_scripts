<?php

namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `http_get(String url, [String method]='GET', [Table data]={}): String`
 *
 * Performs an HTTP request to the given URL using the given method and data.
 * Returns the response. If the content could not be fetched, `nil` is returned.
 *
 * **Note:** Be wary of sending any personal information using this function! Only to be used for fetching templates or other static data.
 */
class Http_Request extends RegistrableFunction {
	public function run($url = '', $method = 'get', $fields = []): ?string {
		$curl = curl_init($url);

		$this->setCurlMethod($curl, $method);
		!empty($fields) && curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		curl_close($curl);

		return $response ?: null;
	}

	private function setCurlMethod(&$curl, string $method) {
		$method = strtoupper(trim($method));
		switch ($method) {
			case 'GET':
				return;
			case 'POST':
				curl_setopt($curl, CURLOPT_POST, 1);
				return;
			case 'PIT':
				curl_setopt($curl, CURLOPT_PUT, 1);
				return;
		}

		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	}
}
