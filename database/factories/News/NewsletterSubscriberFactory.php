<?php

namespace Database\Factories\News;

use App\Models\News\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'subscribed' => fake()->boolean(80),
            'token' => fake()->boolean(20) ? bin2hex(random_bytes(32)) : null,
            'verified_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-6 months', 'now') : null,
        ];
    }

    public function subscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscribed' => true,
            'verified_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'token' => null,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscribed' => true,
            'verified_at' => null,
            'token' => bin2hex(random_bytes(32)),
        ]);
    }
}