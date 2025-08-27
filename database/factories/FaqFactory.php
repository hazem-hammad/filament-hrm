<?php

namespace Database\Factories;

use App\Enum\StatusEnum;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'question' => [
                'en' => $this->faker->sentence() . '?',
                'ar' => 'سؤال تجريبي ' . $this->faker->randomNumber(3) . '؟',
            ],
            'answer' => [
                'en' => $this->faker->paragraph(3),
                'ar' => 'إجابة تجريبية ' . $this->faker->paragraph(2),
            ],
            'status' => StatusEnum::ACTIVE,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::INACTIVE,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::ACTIVE,
        ]);
    }
}