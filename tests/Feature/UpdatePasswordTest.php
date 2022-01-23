<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UpdatePasswordTest extends TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function a_password_can_be_changed_by_user()
	{
		$user = $this->authUser();
		$this->postJson(route('user.password.update'), [
			'oldPassword'           => 'secret123',
			'password'              => '123456',
			'password_confirmation' => '123456',
		])->assertStatus(202);
		$this->assertTrue(Hash::check('123456', $user->fresh()->password));
	}

	/**
	 * @test
	 */
	public function a_password_can_be_not_be_changed_by_user_if_old_password()
	{
		$user = $this->authUser();
		$this->postJson(route('user.password.update'), [
			'oldPassword'           => 'wrongPassword',
			'password'              => '123456',
			'password_confirmation' => '123456',
		])->assertStatus(401);
		$this->assertFalse(Hash::check('123456', $user->fresh()->password));
	}
}
