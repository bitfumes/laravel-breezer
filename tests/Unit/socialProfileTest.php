<?php

namespace Bitfumes\Breezer\Tests\Unit;

use Bitfumes\Breezer\Tests\User;
use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class socialProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_user()
    {
        $user   = $this->createUser();
        $social = $this->createSocial(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $social->user);
    }
}
