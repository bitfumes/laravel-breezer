<?php

namespace Bitfumes\Breezer\Database\Factories;

use Bitfumes\Breezer\SocialProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Bitfumes\Breezer\Tests\Database\Factories\UserFactory;

class SocialProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SocialProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id'               => $this->faker->randomNumber,
            'user_id'          => function () {
                return UserFactory::new()->create()->id;
            },
            'service_id' => 'asdfasdfadsfadsf',
            'avatar'     => $this->faker->url,
            'service'    => 'google',
        ];
    }
}
