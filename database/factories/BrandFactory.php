<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->company(),
            'thumbnail' => ''
        ];
    }
}
