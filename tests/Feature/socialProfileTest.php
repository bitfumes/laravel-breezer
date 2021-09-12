<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class socialProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_when_login_with_github_user_is_created()
    {
        Mail::fake();
        app()->register(SocialiteServiceProvider::class);
        $this->createSocial();
        Socialite::shouldReceive('driver->stateless->user->getId')->andReturn(1);
        Socialite::shouldReceive('driver->stateless->user->getEmail')->andReturn('sarthak@bitfumes.com');
        Socialite::shouldReceive('driver->stateless->user->getName')->andReturn('sarthak');
        Socialite::shouldReceive('driver->stateless->user->getAvatar')->andReturn('avatarURL');

        $this->postJson(route('loginCallback', 'github'), ['code' => 'code']);
        $this->assertDatabaseHas('users', ['name' => 'sarthak']);
    }
}
