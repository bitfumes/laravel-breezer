<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends AuthController
{
    public function __construct()
    {
        $this->user          = app()['config']['breezer.models.user'];
        $this->welcome_email = app()['config']['breezer.welcome_email'];
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resend()
    {
        $user = $this->user::whereEmail(request('email'))->first();
        event(new Registered($user));
        return response('done', 202);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function verifyEmail($id)
    {
        $user = $this->user::find($id);
        if (!$user) {
            return response('No user found. Please click on button to verify your email ID', Response::HTTP_NOT_FOUND);
        }
        if ($this->checkVerifySignature($user)) {
            $user->markEmailAsVerified();
            Mail::to($user->email)->send(new $this->welcome_email($user, null));
            return $this->respondWithToken($user);
        }
        return response('Credential not found or please try to login & resend email.', Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @param $user
     * @return bool
     * @throws \Exception
     */
    protected function checkVerifySignature($user): bool
    {
        return Cache()->get("verify-{$user->id}") === request('signature');
    }
}
