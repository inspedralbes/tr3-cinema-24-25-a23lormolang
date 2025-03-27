<?php

namespace Database\Factories;

use App\Models\Screening;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreeningFactory extends Factory
{
    protected $model = Screening::class;

    public function definition()
    {
        return [
            'movie_id' => \App\Models\Movie::factory(),
            'room_id' => \App\Models\Room::factory(),
            'date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'time' => $this->faker->randomElement(['16:00', '18:30', '20:45', '22:00']),
            'is_special' => $this->faker->boolean
        ];
    }
}