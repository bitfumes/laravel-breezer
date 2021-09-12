<?php

namespace Bitfumes\Breezer\Helpers;

class TokenService
{
    public static function create($user)
    {
        $userResource = config('breezer.resources.user');
        $token        = $user->createToken('web');
        return [
            'access_token' => $token->plainTextToken,
            'user'         => new $userResource($user),
        ];
    }
}
