<?php

return [
    /**
     * Custom Models
     */
    'models' => [
        'user' => \App\Models\User::class,
    ],
    /**
     * Override existing resources
     */
    'resources' => [
        'user' => Bitfumes\Breezer\Http\Resources\UserResource::class,
    ],
    /**
     * Url of your frontend, reset password url and verify email url
     */
    'front_url'          => env('FRONT_URL', 'http://localhost:3000'),
    'reset_url'          => env('BREEZER_RESET_URL', 'password/reset'),
    'verify_url'         => env('BREEZER_VERIFY_URL', 'email/verify'),
    'welcome_email'      => Bitfumes\Breezer\Mail\WelcomeEmail::class,
    'notifications'      => [
        'reset'  => Bitfumes\Breezer\Notifications\UserPasswordReset::class,
        'verify' => Bitfumes\Breezer\Notifications\VerifyEmail::class,
    ],
    'redirect_after_verify' => env('BREEZER_REDIRECT_AFTER_VERIFY', 'http://localhost:3000'),
    /**
     * Avatar Settings
     * Define disk and path to store avatar images
     * Define width and height of thumb image created for user
     */
    'avatar' => [
        'disk'         => env('BREEZER_AVATAR_DISK', 'public'),
        'path'         => env('BREEZER_AVATAR_PATH', 'images/avatars'),
        'thumb_width'  => env('BREEZER_AVATAR_WIDTH', 50),
        'thumb_height' => env('BREEZER_AVATAR_HEIGHT', 50),
    ],
    /**
     * Custom Validation Rules
     * your cusom validation rules for register and update
     * this will merge with existing rules
     */
    'validations' => function () {
        return ['fumesid' =>  'max:10|unique:users,fumesid,' . auth()->id()];
    },
];
