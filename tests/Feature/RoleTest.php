<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_user_role_can_be_added_as_array_of_roles()
    {
        $user = $this->createUser();
        $user->update(['roles' => ['admin']]);
        $this->assertEquals(1, count($user->roles));
    }
}
