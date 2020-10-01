<?php

namespace App\Http\Controllers;
use App\Services\YelpService;

class YelpController extends Controller {

	protected $yelpservice;

	public function __construct(YelpService $yelpservice) {
		$this->yelpservice = $yelpservice;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$businesses = $this->yelpservice->getBusinesses();
		dd($businesses);
	}

}
