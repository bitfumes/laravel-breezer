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
	public function a_password_reset_link_email_can_be_sent()
	{
		$user = $this->authUser();
		$this->postJson(route('user.password.update'), [
			'oldPassword'           => 'secret',
			'password'              => '123456',
			'password_confirmation' => '123456',
		])->assertStatus(202);
		$this->assertTrue(Hash::check('123456', $user->fresh()->password));
	}
}
