<?php

namespace Database\Factories\Blog;

use App\Models\Blog\BlogTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogTagFactory extends Factory
{
    protected $model = BlogTag::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}
