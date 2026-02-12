<?php
// ============================================================================
// FILE 118: database/factories/News/NewsPostFactory.php
// ============================================================================

namespace Database\Factories\News;

use App\Models\News\NewsPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsPostFactory extends Factory
{
    protected $model = NewsPost::class;

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
            'cover_photo_path' => 'news/covers/placeholder.jpg',
            'photo_alt_text' => fake()->sentence(5),
            'views' => fake()->numberBetween(0, 10000),
            'read_time' => fake()->numberBetween(3, 15) . ' min',
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

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'published_at' => null,
            'scheduled_for' => fake()->dateTimeBetween('now', '+1 month'),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}