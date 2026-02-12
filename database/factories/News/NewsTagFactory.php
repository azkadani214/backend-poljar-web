<?php


namespace Database\Factories\News;

use App\Models\News\NewsTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsTagFactory extends Factory
{
    protected $model = NewsTag::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}