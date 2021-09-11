<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetEmailController extends Controller
{
    public function __construct()
    {
        $this->user= app()['config']['breezer.models.user'];
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = $this->user::whereEmail($request->email)->first();

        if ($user) {
            $token = Password::createToken($user);
            $user->sendPasswordResetNotification($token);
        }

        return response(['message' => 'We have emailed your password reset link!'], Response::HTTP_ACCEPTED);
    }
}
