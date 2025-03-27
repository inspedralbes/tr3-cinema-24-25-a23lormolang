<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use App\Models\Screening;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class MovieTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->movieData = [
            'imdbID' => 'tt1234567',
            'Title' => 'Test Movie',
            'Plot' => 'Test plot',
            'Runtime' => '120 min',
            'Poster' => 'http://example.com/poster.jpg',
            'Year' => '2023',
            'Genre' => 'Action',
            'Director' => 'Test Director',
            'Actors' => 'Actor 1, Actor 2',
            'Awards' => 'Oscar winner',
            'imdbRating' => '8.5',
            'BoxOffice' => '$100,000,000'
        ];
    }

    #[Test]
    public function can_search_movies_in_omdb()
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'omdbapi.com/*' => Http::response([
                'Search' => [
                    [
                        'Title' => 'Test Movie',
                        'Year' => '2023',
                        'imdbID' => 'tt1234567',
                        'Type' => 'movie',
                        'Poster' => 'http://example.com/poster.jpg'
                    ]
                ]
            ])
        ]);

        $response = $this->getJson('/api/omdb/search?query=test');

        $response->assertStatus(200)
            ->assertJsonStructure([[
                'Title',
                'Year',
                'imdbID',
                'Type',
                'Poster'
            ]]);
    }

    #[Test]
    public function can_create_movie_from_omdb()
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'omdbapi.com/*' => Http::response(array_merge(
                ['Response' => 'True'],
                $this->movieData
            ))
        ]);

        $response = $this->postJson('/api/movies', [
            'imdb_id' => 'tt1234567'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'duration',
                'poster_url',
                'year',
                'genre',
                'director'
            ]);

        $this->assertDatabaseHas('movies', [
            'imdb_id' => 'tt1234567',
            'title' => 'Test Movie'
        ]);
    }

    #[Test]
    public function returns_existing_movie_if_already_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $movie = Movie::factory()->create(['imdb_id' => 'tt1234567']);

        $response = $this->postJson('/api/movies', [
            'imdb_id' => 'tt1234567'
        ]);

        $response->assertStatus(201)
            ->assertJson(['id' => $movie->id]);
    }

    #[Test]
    public function returns_error_for_invalid_omdb_id()
    {
        Sanctum::actingAs(User::factory()->create());

        Http::fake([
            'omdbapi.com/*' => Http::response(['Response' => 'False'])
        ]);

        $response = $this->postJson('/api/movies', [
            'imdb_id' => 'invalid_id'
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Película no encontrada en OMDB']);
    }

    #[Test]
    public function can_get_movie_with_future_screenings()
    {
        $movie = Movie::factory()->create();
        $room = Room::factory()->create(['total_seats' => 100]);
        
        // Screening futuro
        $futureScreening = Screening::factory()->create([
            'movie_id' => $movie->id,
            'room_id' => $room->id,
            'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'time' => '18:00'
        ]);
        
        // Screening pasado
        Screening::factory()->create([
            'movie_id' => $movie->id,
            'room_id' => $room->id,
            'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'time' => '18:00'
        ]);

        $response = $this->getJson("/api/movies/{$movie->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'screenings' => [[
                    'id',
                    'date',
                    'time',
                    'is_special',
                    'room' => [
                        'id',
                        'name',
                        'has_vip',
                        'availableSeats'
                    ]
                ]]
            ])
            ->assertJsonCount(1, 'screenings');
    }
}