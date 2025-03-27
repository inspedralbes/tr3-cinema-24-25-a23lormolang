<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition()
    {
        return [
            'imdb_id' => 'tt'.$this->faker->unique()->numerify('#######'),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'duration' => $this->faker->numberBetween(60, 180),
            'poster_url' => $this->faker->optional()->imageUrl(),
            'year' => $this->faker->year,
            'genre' => $this->faker->randomElement(['Action', 'Drama', 'Comedy']),
            'director' => $this->faker->name,
            'actors' => implode(', ', $this->faker->words(3)),
            'awards' => $this->faker->optional()->sentence,
            'imdb_rating' => $this->faker->optional()->randomFloat(1, 0, 10),
            'box_office' => $this->faker->optional()->bothify('$## million'),
        ];
    }
}