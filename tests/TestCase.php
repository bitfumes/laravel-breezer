<?php

namespace Bitfumes\Breezer\Tests;

use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\SanctumServiceProvider;
use Bitfumes\Breezer\BreezerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Bitfumes\Breezer\Tests\Database\Factories\UserFactory;
use Bitfumes\Breezer\Database\Factories\SocialProfileFactory;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->loadMigrations();
    }

    protected function loadMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']); // package migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations'); // test migrations
        $this->loadMigrationsFrom(__DIR__ . '/../src/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/laravel/sanctum/database/migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('breezer.models.user', User::class);
        $app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [BreezerServiceProvider::class, SanctumServiceProvider::class];
    }

    public function createUser($args = [], $num = null)
    {
        return UserFactory::new()->count($num)->create($args);
    }

    public function createSocial($args = [], $num = null)
    {
        return SocialProfileFactory::new()->count($num)->create($args);
    }

    public function authUser()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);
        return $user;
    }
}
