<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Swift_TransportException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Bitfumes\Breezer\Http\Requests\EmailVerificationRequest;

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
        $user->sendEmailVerificationNotification();
        return response('done', 202);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function verifyEmail(EmailVerificationRequest $request)
    {
        if (!$user = $request->validate()) {
            return response([
                'errors'=> ['error'=>'Credential not found or please try to login & resend email.'],
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->markEmailAsVerified()) {
            try {
                Mail::to($user->email)->send(new $this->welcome_email($user, null));
            } catch (Swift_TransportException $e) {
                Log::error($e->getMessage());
                return response(['errors' => ['error'=> 'Could not send email, try again', ],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return redirect(config('breezer.redirect_after_verify'));
        }
    }
}
