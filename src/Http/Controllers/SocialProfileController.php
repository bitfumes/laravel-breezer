<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Bitfumes\Breezer\SocialProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Bitfumes\Breezer\Helpers\TokenService;
use Symfony\Component\HttpFoundation\Response;

class SocialProfileController extends AuthController
{
    public function __construct()
    {
        $this->user = config('breezer.models.user');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($service)
    {
        try {
            return Socialite::driver($service)
                ->stateless()
                ->redirect()
                ->getTargetUrl();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(['errors' => ['error' => 'There is some issue with login.']], Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($service)
    {
        $details = Socialite::driver($service)->stateless()->user();
        return $this->userProfile($details, $service);
    }

    /**
     * @param $details
     * @param $service
     * @return \Illuminate\Http\JsonResponse
     */
    protected function userProfile($details, $service)
    {
        $social = $this->checkSocialProfile($details);

        $user  = $social ?
            $this->user::find($social->user_id) :
            $this->createSocialProfile($details, $service);

        return response(TokenService::create($user));
    }

    /**
     * @param $details
     * @return mixed
     */
    protected function checkSocialProfile($details)
    {
        return SocialProfile::where('service_id', $details->getId())->first();
    }

    /**
     * @param $details
     * @param $service
     * @return User
     */
    protected function createSocialProfile($details, $service)
    {
        $user = $this->user::where('email', $details->getEmail())->first();
        if (!$user) {
            $user = $this->createUser($details);
        }
        $social             = new SocialProfile();
        $social->service_id = $details->getId();
        $social->service    = $service;
        $social->user_id    = $user->id;
        $social->avatar     = $details->getAvatar();
        $social->save();
        return $user;
    }

    /**
     * @param $details
     * @return User
     */
    protected function createUser($details)
    {
        $user                       = new $this->user();
        $user->name                 = $details->getName() ?? $details->getEmail();
        $user->email                = $details->getEmail();
        $user->email_verified_at    = Carbon::now();
        $randomPassword             = Str::random(10);
        $user->password             = bcrypt($randomPassword);
        $user->save();
        $this->sendWelcomeEmail($user, $randomPassword);
        return $user;
    }

    /**
     * @param $user
     * @param $password
     */
    public function sendWelcomeEmail($user, $password)
    {
        $welcomeEmail = config('breezer.welcome_email');
        Mail::to($user->email)->send(new $welcomeEmail($user, $password));
    }
}
