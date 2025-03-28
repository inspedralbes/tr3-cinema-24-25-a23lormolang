<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition()
    {
        return [
            'row' => strtoupper($this->faker->randomLetter),
            'number' => $this->faker->unique()->numberBetween(1, 50),
            'type' => $this->faker->randomElement(['normal', 'vip']),
            'room_id' => Room::factory()
        ];
    }
}