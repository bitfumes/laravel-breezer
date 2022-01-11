<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\User;
use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Bitfumes\Breezer\Tests\Database\Factories\UserFactory;

class RegisterTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function a_user_can_register_and_get_email_confirmation()
	{
		$user = UserFactory::new()->raw();
		Event::fake();
		$res = $this->postJson(route('user.register'), [
			'name'                  => $user['name'],
			'email'                 => $user['email'],
			'roles'                 => ['admin'],
			'password'              => 'secret123',
			'password_confirmation' => 'secret123',
		])->assertCreated();

		$user = User::first();
		Event::assertDispatched(Registered::class);
		$this->assertTrue(Hash::check('secret123', $user['password']));
		$this->assertDatabaseHas('users', ['email' => $user['email'], 'email_verified_at' => null, 'roles' => "[\"admin\"]"]);
	}

	/** @test */
	public function for_registration_email_password_and_name_is_required()
	{
		$this->withExceptionHandling();
		$res = $this->postJson(route('user.register'));
		$res->assertJsonValidationErrors(['email', 'password', 'name']);
	}

	/** @test */
	public function for_registration_email_must_be_real_email()
	{
		$this->withExceptionHandling();
		$res = $this->post(route('user.register'), ['email' => 'sarthak.com']);
		$this->assertEquals(session('errors')->get('email')[0], 'The email must be a valid email address.');
	}

	/** @test */
	public function for_registration_password_must_be_min_of_8_chars()
	{
		$this->withExceptionHandling();
		$res = $this->post(route('user.register'), ['password' => 'abcd', 'password_confirmation' => 'abcd']);
		$this->assertEquals(session('errors')->get('password')[0], 'The password must be at least 8 characters.');
	}

	/** @test */
	public function for_registration_name_must_be_max_of_25_chars()
	{
		$this->withExceptionHandling();
		$res = $this->post(route('user.register'), ['name' => 'ankur sarthak shrivastava savvy']);
		$this->assertEquals(session('errors')->get('name')[0], 'The name may not be greater than 25 characters.');
	}

	/** @test */
	public function a_user_can_login_with_email_and_password()
	{
		$user = $this->createUser();
		$user->update(['email_verified_at' => now()]);
		$res = $this->postJson(route('user.login'), ['email' => $user->email, 'password' => 'secret123'])->json();
		$this->assertNotNull($res['access_token']);
	}

	/** @test */
	public function for_login_email_password_required()
	{
		$this->withExceptionHandling();
		$res = $this->post(route('user.login'));
		$res->assertSessionHasErrors(['email', 'password']);
	}

	/** @test */
	public function for_login_email_must_be_real_email()
	{
		$this->withExceptionHandling();
		$res = $this->post(route('user.login'), ['email' => 'sarthak.com']);
		$this->assertEquals(session('errors')->get('email')[0], 'The email must be a valid email address.');
	}

	/** @test */
	public function for_login_password_must_be_min_of_8_chars()
	{
		$this->withExceptionHandling();
		$res = $this->post(route('user.login'), ['password' => 'abcd', 'password_confirmation' => 'abcd']);
		$this->assertEquals(session('errors')->get('password')[0], 'The password must be at least 8 characters.');
	}
}
