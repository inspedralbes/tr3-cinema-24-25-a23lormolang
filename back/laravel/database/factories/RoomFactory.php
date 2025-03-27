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
            'has_vip' => $this->faker->boolean,
            'total_seats' => $this->faker->numberBetween(50, 200),
            'vip_seats' => function (array $attributes) {
                return $attributes['has_vip']
                    ? $this->faker->numberBetween(10, 50)
                    : 0;
            }
        ];
    }   
}