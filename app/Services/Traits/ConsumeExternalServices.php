<?php

namespace App\Services\Traits;
use App\Services\AuthenticationService;
use GuzzleHttp\Client;

trait ConsumeExternalServices {

	/**
	 * Global service request method
	 * @param  $method    reuquest methid
	 * @param  $requestUrl  request url
	 * @param  array   $queryParams  query params
	 * @param  array   $formParams  form params
	 * @param  array   $headers     headers
	 * @param  boolean $hasFile     bodyType
	 * @return stdClass
	 */
	protected function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $hasFile = false): object{

		$client = new Client([
			'base_uri' => $this->base_uri,
		]);

		if (method_exists($this, 'resolveAuth')) {
			$this->resolveAuth($queryParams, $formParams, $header);
		}

		$bodyType = "form_params";

		if ($hasFile) {
			$bodyType = "multipart";

			$multipart = [];

			foreach ($formParams as $name => $contents) {
				$multipart[] = ["name" => $name, "contents" => $contents];
			}
		}

		$response = $client->request($method, $requestUrl, [
			'query' => $queryParams,
			$bodyType => $hasFile ? $multipart : $formParams,
			'headers' => $headers,
		]);

		$response = $response->getBody()->getContents();

		//decode response. Data to stdClass
		if (method_exists($this, 'decodeReposne')) {
			$this->decodeResponse($response);
		}

		//Check for error. Some services return 200 response for error.
		if (method_exists($this, 'checkErrorResponse')) {
			$this->checkErrorResponse($response);
		}

		return $response;
	}

	/**
	 * Resolve authorization
	 * @param  $queryParams
	 * @param  &$formParams
	 * @param  &$headers
	 * @return void
	 */
	protected function resolveAuth(&$queryParams, &$formParams, &$headers): void{
		$accessToken = $this->resolveAccessToken();
		$headers['Authorization'] = $accessToken;
	}

	/**
	 * Resolve accesstoken.
	 * @return string
	 */
	private function resolveAccessToken(): string{
		$serviceAuth = resolve(AuthenticationService::class);
		return $serviceAuth->getAccessToken();
	}

	/**
	 *Decode response. Some client accept array or plain text
	 * @param  $response client response
	 * @return stdClass
	 */
	protected function decodeReposne($response): object{
		$responseData = json_decode($response);
		return $responseData->data ?? $responseData;
	}

	/**
	 * Some clients return 200 error response
	 * @param  $response 200 error response
	 * @return void
	 */
	protected function checkErrorResponse($response): void {
		if (isset($response->error)) {
			throw new \Exception("Something failed {$response->error}");
		}
	}
}

?>