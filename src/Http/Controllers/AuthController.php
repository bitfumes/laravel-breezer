<?php

namespace Bitfumes\Breezer\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\Registered;
use Symfony\Component\HttpFoundation\Response;
use Bitfumes\Breezer\Http\Requests\UpdateRequest;
use Bitfumes\Breezer\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('getUser');
        $this->resource = app()['config']['breezer.resources.user'];
        $this->user     = app()['config']['breezer.models.user'];
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->user::create($request->validatedFields());
        event(new Registered($user));
        return response(['message' => 'Now verify your email ID to activate your account'], Response::HTTP_CREATED);
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function update(UpdateRequest $request)
    {
        $user = auth()->user();
        $user->update($request->except('avatar'));
        $this->checkForAvatar($request, $user);
        return response([
            'data' => new $this->resource($user),
        ], Response::HTTP_ACCEPTED);
    }

    protected function checkForAvatar($request, $user)
    {
        if ($request->has('avatar')) {
            $user->uploadProfilePic($request->avatar);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function getUser()
    {
        $user = auth()->user();
        return response([
            'data' => new $this->resource($user),
        ], Response::HTTP_ACCEPTED);
    }
}
