<?php

namespace App\Services;
use App\Services\Traits\ConsumeExternalServices;

class YelpService {

	use ConsumeExternalServices;

	protected $base_uri;

	public function __construct() {
		$this->base_uri = config('services.yelp.base_uri');
	}

	public function getBusinesses() {
		$response = $this->makeRequest("GET", "/businesses/search", ['term' => 'restaurants']);
	}
}