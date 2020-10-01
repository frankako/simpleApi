<?php
namespace App\Traits;
//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ApiResponseTrait {

	protected function successResponse($data, $code) {
		return response()->json($data, $code);
	}

	protected function errorResponse($data, $code) {
		return response()->json(['error' => $data, 'code' => $code], $code);
	}

	protected function allResult(Collection $collection, $code = 200) {
		return $this->successResponse(['data' => $collection], $code);
	}

	protected function oneResult(Model $model, $code = 200) {
		return $this->successResponse(['data' => $model], $code);
	}

}

?>