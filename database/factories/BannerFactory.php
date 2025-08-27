<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'en' => $this->faker->sentence(3),
                'es' => $this->faker->sentence(3),
            ],
            'status' => $this->faker->randomElement([1, 0]), // Active or Inactive
            'sort_order' => $this->faker->numberBetween(0, 100),
            'date_from' => $this->faker->optional()->date(),
            'date_to' => $this->faker->optional()->date(),
        ];
    }
}
