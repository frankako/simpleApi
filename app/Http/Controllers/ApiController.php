<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponseTrait;

class ApiController extends Controller {

	use ApiResponseTrait;

	public function __construct() {
		$this->middleware('auth:api');
	}
}
