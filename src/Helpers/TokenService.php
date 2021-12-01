<?php

namespace Bitfumes\Breezer\Helpers;

class TokenService
{
    public static function create($user, $tokenName = 'web')
    {
        $userResource = config('breezer.resources.user');
        $token        = $user->createToken($tokenName);
        return [
            'access_token' => $token->plainTextToken,
            'user'         => new $userResource($user),
        ];
    }
}
