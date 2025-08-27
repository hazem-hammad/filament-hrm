<?php

namespace Database\Factories;

use App\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
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
                'ar' => $this->faker->sentence(4),
            ],
            'body' => [
                'en' => $this->faker->paragraphs(3, true),
                'es' => $this->faker->paragraphs(3, true),
                'ar' => $this->faker->paragraphs(3, true),
            ],
            'slug' => $this->faker->unique()->slug(3),
            'status' => StatusEnum::ACTIVE,
        ];
    }

    /**
     * Indicate that the page is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => StatusEnum::INACTIVE,
        ]);
    }
}
