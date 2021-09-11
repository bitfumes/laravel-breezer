<?php

namespace Bitfumes\Breezer\Tests\Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = app()['config']['breezer.models.user'];
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email_verified_at'      => now(),
            'avatar'                 => $this->faker->imageUrl(),
            'name'                   => $this->faker->name,
            'email'                  => $this->faker->unique()->safeEmail,
            'avatar'                 => $this->faker->imageUrl(),
            'password'               => '$2y$04$7Rghdz2qIqjogyM79epcFOaEo9DXcdsVJHDmq3KrVFUbjfrMKKHYC', // secret123
            'remember_token'         => Str::random(10),
        ];
    }
}
