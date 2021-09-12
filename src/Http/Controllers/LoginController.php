<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Bitfumes\Breezer\Helpers\TokenService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpFoundation\Response;
use Bitfumes\Breezer\Http\Requests\LoginRequest;

class LoginController extends AuthController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('logout');
        $this->user= app()['config']['breezer.models.user'];
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        return $this->performLogin($request);
    }

    /**
     * @param $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    protected function performLogin($request)
    {
        $user  = $this->user::whereEmail($request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->noTokenResponse();
        }

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return $this->emailNotVerifiedResponse();
        }
        return response(TokenService::create($user));
    }

    /**
     * @return JsonResponse
     */
    protected function noTokenResponse(): JsonResponse
    {
        return response()->json([
            'errors' => [
                'error' => 'Credentials does\'t match our record',
            ],
        ], 401);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    protected function emailNotVerifiedResponse()
    {
        return response(['errors' => ['verify' => 'Please verify your email first.']], Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
