<?php

use Illuminate\Support\Facades\Route;

Route::post('register', 'AuthController@register')->name('user.register');
Route::post('login', 'LoginController@login')->name('user.login');
Route::post('logout', 'LoginController@logout')->name('logout');
Route::get('user', 'AuthController@getUser')->name('user.get');
Route::patch('user', 'AuthController@update')->name('user.update');

// email verification
Route::post('/email/verify/resend', 'VerifyEmailController@resend')->name('verification.resend');
Route::get('/email/verify/{id}', 'VerifyEmailController@verifyEmail')->name('verification.verify');

// Password Resets
Route::post('/forgot-password', 'PasswordResetEmailController@store')->name('user.password.email');
Route::post('/password/reset', 'ResetPasswordController@store')->name('user.password.reset');

// Update Password
Route::post('/password/update', 'UpdatePasswordController')->name('user.password.update');

// Social Login
Route::post('social-login/{service}', 'SocialProfileController@redirectToProvider');
Route::post('social-login/{service}/callback', 'SocialProfileController@handleProviderCallback')->name('loginCallback');
