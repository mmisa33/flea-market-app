<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'profile_image' => 'default-profile.jpg',
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
        ];
    }
}