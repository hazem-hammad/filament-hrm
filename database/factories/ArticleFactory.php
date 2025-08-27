<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
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
                'en' => $this->faker->sentence(4),
                'es' => $this->faker->sentence(4),
            ],
            'content' => [
                'en' => $this->faker->paragraphs(3, true),
                'es' => $this->faker->paragraphs(3, true),
            ],
            'date' => $this->faker->date(),
            'status' => $this->faker->randomElement([1, 0]), // Active or Inactive
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
