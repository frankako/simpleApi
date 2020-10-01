<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Notifications\UserCreated;
use App\Notifications\UserEmailUpdated;
use App\User;
use Illuminate\Http\Request;

class UserController extends ApiController {

	public function __construct() {
		$this->middleware('auth:api')->except(['show', 'store']);
		$this->middleware('client.credentials')->only(['show', 'store']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$users = User::all();
		return $this->allResult($users);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$rules = [
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|confirmed',
		];

		$this->validate($request, $rules);
		$data = $request->all();
		$data['password'] = bcrypt($request->password);
		$user = User::create($data);

		$user->notify(new UserCreated($user));

		return $this->oneResult($user);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(User $user) {
		return $this->oneResult($user);
	}

	/**
	 * Update the specified resource in storage
	 * using implicit model binding.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  stdClass $user
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, User $user) {

		$userData = ['name', 'email']; //when dealing with much data
		foreach ($userData as $inputName) {
			if ($request->has($inputName)) {
				$user->{$inputName} = $request->{$inputName};
			}
		}

		if ($request->has('password')) {
			$user->password = bcrypt($request->password);
		}

		if ($user->isClean()) {
			return $this->errorResponse("You have to make a change to update", 422);
		}

		if ($user->isDirty('email')) {
			$user->notify(new UserEmailUpdated($user));
		}

		$user->save();

		return $this->oneResult($user);

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(User $user) {
		$user->delete();
		return $this->oneResult($user);
	}

	/**
	 * Show password user
	 * @param  \Illuminate\Http\Request $request
	 */
	public function notifications(User $user) {
		$user->unreadNotifications->markAsRead();
		return $this->allResult($user->notifications);
	}

	public function unreadnotifications(User $user) {
		return $this->allResult($user->unreadNotifications);
	}
}
