<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdatePasswordController extends AuthController
{
	/**
	 * UpdatePasswordController constructor.
	 */
	public function __construct()
	{
		$this->middleware('auth:sanctum');
	}

	public function __invoke(Request $request)
	{
		$data = $request->validate([
			'oldPassword' => 'required',
			'password'    => 'required|confirmed',
		]);

		auth()->user()->update(['password' => bcrypt($data['password'])]);
		return response('password successfully changed', Response::HTTP_ACCEPTED);
	}
}
