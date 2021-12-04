<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function api_can_update_user_details()
    {
        $user = $this->authUser();
        $res  = $this->patchJson(route('user.update'), [
            'name'  => $user->name,
            'email' => 'abc@def.com', ]);
        $this->assertEquals($user->name, json_decode($res->getContent())->data->name);
    }
}
