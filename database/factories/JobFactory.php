<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'department_id' => 1, // We'll need to seed departments
            'position_id' => 1,   // We'll need to seed positions
            'number_of_positions' => $this->faker->numberBetween(1, 5),
            'work_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'internship']),
            'work_mode' => $this->faker->randomElement(['remote', 'onsite', 'hybrid']),
            'experience_level' => $this->faker->randomElement(['entry', 'junior', 'mid', 'senior', 'lead']),
            'status' => true,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'short_description' => $this->faker->sentence(),
            'long_description' => $this->faker->paragraphs(3, true),
            'job_requirements' => $this->faker->paragraphs(2, true),
            'benefits' => $this->faker->paragraphs(2, true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
