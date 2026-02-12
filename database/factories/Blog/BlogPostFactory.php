<?php

namespace Database\Factories\Blog;

use App\Models\Blog\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);
        
        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'sub_title' => fake()->sentence(10),
            'body' => fake()->paragraphs(10, true),
            'excerpt' => fake()->paragraph(3),
            'status' => fake()->randomElement(['published', 'draft', 'scheduled']),
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year', 'now') : null,
            'scheduled_for' => null,
            'cover_photo_path' => 'blog/covers/placeholder.jpg',
            'photo_alt_text' => fake()->sentence(5),
            'views' => fake()->numberBetween(0, 5000),
            'read_time' => fake()->numberBetween(2, 10) . ' min',
            'is_featured' => fake()->boolean(10),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }
}
