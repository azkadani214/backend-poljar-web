<?php
namespace Database\Factories\News;

use App\Models\News\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsCategoryFactory extends Factory
{
    protected $model = NewsCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'color' => fake()->randomElement([
                'bg-blue-100 text-blue-800',
                'bg-green-100 text-green-800',
                'bg-red-100 text-red-800',
                'bg-yellow-100 text-yellow-800',
                'bg-purple-100 text-purple-800',
                'bg-pink-100 text-pink-800',
                'bg-indigo-100 text-indigo-800',
            ]),
            'description' => fake()->sentence(15),
        ];
    }
}