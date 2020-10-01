<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler {
	use ApiResponseTrait;
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param  \Throwable  $exception
	 * @return void
	 *
	 * @throws \Throwable
	 */
	public function report(Throwable $exception) {
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 * Handle exception for json
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Throwable
	 */
	public function render($request, Throwable $exception) {

		if ($exception instanceof ValidationException) {
			return $this->convertValidationExceptionToResponse($exception, $request);
		}

		if ($exception instanceof ModelNotFoundException) {
			$modelName = strtolower(class_basename($exception->getModel()));
			return $this->errorResponse("There is no resource for the model url: {$modelName}", 404);
		}

		if ($exception instanceof AuthenticationException) {
			return $this->unauthenticated($request, $exception);
		}

		if ($exception instanceof AuthorizationException) {
			return $this->errorResponse($exception->getMessage(), 403);
		}

		if ($exception instanceof NotFoundHttpException) {
			return $this->errorResponse("The specified url can not be found", 404);
		}

		if ($exception instanceof MethodNotAllowedHttpException) {
			return $this->errorResponse("The specified request method {$request->method()} is invalid for path {$request->path()}", 405);
		}

		if ($exception instanceof TokenMismatchException) {
			return redirect()->back()->withInput($request->input());
		}

		if ($exception instanceof HttpException) {
			$this->errorResponse($exception->getMessage(), $exception->getStatusCode());
		}

		if (config('app.debug')) {
			return parent::render($request, $exception);
		}

		return $this->errorResponse("An unexpected error occured!", 500);
	}

	/**
	 * Convert an authentication exception into a response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Auth\AuthenticationException  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception) {

		if ($this->isFrontEnd($request)) {
			return redirect()->guest('login');
		}

		return $this->errorResponse("Unauthenticated", 401);
	}

	/**
	 * convertValidationExceptionToResponse from parent render method
	 * @param  ValidationException $e       exception
	 * @param  $request request
	 * @return
	 */
	protected function convertValidationExceptionToResponse(ValidationException $e, $request) {

		$errors = $e->errors();

		if ($this->isFrontEnd($request)) {
			return $request->ajax() ? response()->json($errors, 422) : redirect()->back()
				->withInput($request->input())->withErrors($errors);
		}

		return $this->errorResponse($errors, 422);
	}

	/**
	 * check if request isFrontend to handle
	 * Api and HTML exceptions
	 * @param  $request request
	 * @return boolean
	 */
	private function isFrontEnd($request) {
		return $request->acceptsHTML() && collect($request->route()->middleware())->contains('web');
	}
}
