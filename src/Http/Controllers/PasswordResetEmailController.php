<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Http\Request;
use Swift_TransportException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
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
            try {
                $user->sendPasswordResetNotification($token);
            } catch (Swift_TransportException $e) {
                Log::error($e->getMessage());
                return response(['errors' => ['error'=> 'Could not send email, try again', ],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return response(['message' => 'We have emailed your password reset link!'], Response::HTTP_ACCEPTED);
    }
}
