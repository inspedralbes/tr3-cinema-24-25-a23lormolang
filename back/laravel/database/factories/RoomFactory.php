<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        return [
            'name' => 'Sala ' . $this->faker->randomNumber(2),
            'total_seats' => $this->faker->numberBetween(50, 200),
            'has_vip' => $this->faker->boolean,
            'vip_seats' => function (array $attributes) {
                return $attributes['has_vip'] ? $this->faker->numberBetween(10, 30) : 0;
            },
        ];
    }
}