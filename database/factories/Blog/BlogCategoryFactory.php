<?php

namespace Database\Factories\Blog;

use App\Models\Blog\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'color' => fake()->hexColor(),
            'description' => fake()->sentence(15),
        ];
    }
}
