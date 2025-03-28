<?php

namespace Database\Factories;

use App\Models\Screening;
use App\Models\Room;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreeningFactory extends Factory
{
    protected $model = Screening::class;

    public function definition()
    {
        return [
            'movie_id' => Movie::factory(),
            'room_id' => Room::factory(),
            'date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'time' => $this->faker->randomElement(['16:00', '18:30', '20:45', '22:00']),
            'is_special' => $this->faker->boolean
        ];
    }
}