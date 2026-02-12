<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'division_id' => Division::factory(),
            'position_id' => function (array $attributes) {
                return Position::factory()->create(['division_id' => $attributes['division_id']])->id;
            },
            'is_active' => fake()->boolean(90),
            'period' => '2023/2024',
        ];
    }
}
