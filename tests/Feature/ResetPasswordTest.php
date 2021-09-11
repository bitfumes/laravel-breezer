<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Bitfumes\Breezer\Notifications\UserPasswordReset;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ResetPasswordTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_password_reset_link_email_can_be_sent()
    {
        Notification::fake();
        $user = $this->createUser();
        $this->postJson(route('user.password.email'), ['email' => $user->email])->assertStatus(202);
        Notification::assertSentTo([$user], UserPasswordReset::class);
    }

    /** @test */
    public function a_user_can_change_its_password()
    {
        Notification::fake();
        $user = $this->createUser();

        $this->post(route('user.password.email'), ['email' => $user->email]);
        Notification::assertSentTo([$user], UserPasswordReset::class, function ($notification) use ($user) {
            $token = $notification->token;
            $this->assertTrue(Hash::check('secret123', $user->password));

            $this->post(route('user.password.reset'), [
                'email'                 => $user->email,
                'password'              => 'newpassword',
                'password_confirmation' => 'newpassword',
                'token'                 => $token,
            ])->assertStatus(202);
            $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
            return true;
        });
    }
}
