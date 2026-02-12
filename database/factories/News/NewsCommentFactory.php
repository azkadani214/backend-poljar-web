<?php

namespace Database\Factories\News;

use App\Models\News\NewsComment;
use App\Models\News\NewsPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsCommentFactory extends Factory
{
    protected $model = NewsComment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'news_post_id' => NewsPost::factory(),
            'comment' => fake()->paragraph(3),
            'approved' => fake()->boolean(70),
            'approved_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved' => true,
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approved' => false,
            'approved_at' => null,
        ]);
    }
}