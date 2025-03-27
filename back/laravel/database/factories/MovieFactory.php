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
            'imdb_id' => 'tt' . $this->faker->unique()->numerify('#######'), // Ej: tt1234567
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'duration' => $this->faker->numberBetween(60, 180),
            'poster_url' => $this->faker->optional()->imageUrl(300, 450, 'movies'),
            'year' => $this->faker->year('-2 years'),
            'genre' => $this->faker->randomElement(['Acción', 'Drama', 'Comedia', 'Ciencia Ficción', 'Terror']),
            'director' => $this->faker->name,
            'actors' => implode(', ', $this->faker->randomElements([
                'Tom Hanks', 'Meryl Streep', 'Leonardo DiCaprio', 
                'Scarlett Johansson', 'Denzel Washington'
            ], 3)),
            'awards' => $this->faker->optional(0.3)->sentence,
            'imdb_rating' => $this->faker->optional()->randomFloat(1, 0, 10),
            'box_office' => $this->faker->optional(0.2)->bothify('$## million'),
        ];
    }
}