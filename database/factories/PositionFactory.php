<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'division_id' => Division::factory(),
            'name' => fake()->jobTitle(),
            'level' => fake()->numberBetween(1, 10),
        ];
    }
}
