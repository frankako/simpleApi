<?php

namespace App\Services;

class AuthenticationService {

	protected $accessToken;

	public function __construct() {
		$this->accessToken = config("services.yelp.access_token");
	}

	public function getAccessToken() {
		return $this->accessToken;
	}
}