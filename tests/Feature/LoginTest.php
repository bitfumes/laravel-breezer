<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_provide_user_details()
    {
        $this->authUser();
        $user = auth()->user();
        $res  = $this->getJson(route('user.get'));
        $this->assertEquals($user->name, json_decode($res->getContent())->data->name);
    }

    /** @test */
    public function user_can_login_and_then_logout()
    {
        $user  = $this->authUser();
        $user->update(['email_verified_at' => now()]);
        $this->postJson(route('user.login'), ['email'=>$user->email, 'password'=>'secret123']);
        $this->assertTrue(auth()->check());
        $this->assertEquals(1, $user->tokens()->count());
        $this->postJson(route('logout'));
        $this->assertEquals(0, $user->tokens()->count());
    }
}
