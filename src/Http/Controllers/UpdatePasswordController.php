<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

		$user = auth()->user();

		if (! $user || ! Hash::check($request->oldPassword, $user->password)) {
			return response()->json([
				'errors' => [
					'error' => 'Credentials does\'t match our record',
				],
			], 401);
		}

		auth()->user()->update(['password' => bcrypt($data['password'])]);
		return response('password successfully changed', Response::HTTP_ACCEPTED);
	}
}
