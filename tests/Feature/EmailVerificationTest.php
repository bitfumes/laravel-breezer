<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Bitfumes\Breezer\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function setup():void
    {
        parent::setup();
        Mail::fake();
        Notification::fake();
        $this->user = $this->authUser(['email_verified_at' => null]);
        $this->user->sendEmailVerificationNotification();
    }

    /** @test */
    public function a_user_get_403_if_user_not_found_via_id_while_verifying_email()
    {
        Notification::assertSentTo($this->user, VerifyEmail::class, function () {
            $this->withExceptionHandling();
            $this->getJson(route('verification.verify', ['id'=> $notInDBUserId = 4000]))->assertForbidden();

            $this->assertNull($this->user->fresh()->email_verified_at);
            return true;
        });
    }

    /** @test */
    public function a_user_get_403_if_user_signature_is_incorrect()
    {
        Notification::assertSentTo($this->user, VerifyEmail::class, function () {
            $this->withExceptionHandling();
            $expires = now()->addMinutes(60)->timestamp;

            $this->getJson(route('verification.verify', ['id'=> $this->user->id, 'signature' => 'incorrect', 'expires' =>$expires]))->assertForbidden();

            $this->assertNull($this->user->fresh()->email_verified_at);
            return true;
        });
    }

    /** @test */
    public function user_can_resend_verify_email()
    {
        $this->postJson(route('verification.resend', $this->user->toArray()))->assertStatus(202);
        Notification::assertSentTo($this->user, VerifyEmail::class);
    }

    /** @test */
    public function user_can_verify_its_email()
    {
        Notification::assertSentTo($this->user, VerifyEmail::class, function () {
            $this->withExceptionHandling();
            $this->getJson(route('verification.verify', 22))->assertForbidden();

            $this->withoutExceptionHandling();
            $expires = now()->addMinutes(60)->timestamp;
            $sign = sha1($expires . $this->user->getEmailForVerification());
            $this->getJson(route('verification.verify', ['id'=> $this->user->id, 'signature' => $sign, 'expires' =>$expires]))->assertRedirect();

            $this->assertNotNull($this->user->fresh()->email_verified_at);
            return true;
        });
    }
}
