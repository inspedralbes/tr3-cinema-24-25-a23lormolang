<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Room;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ScreeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->movie = Movie::factory()->create();
        $this->room = Room::factory()->create();

        // Crear asientos con combinaciones únicas
        for ($i = 1; $i <= 10; $i++) {
            Seat::factory()->create([
                'room_id' => $this->room->id,
                'row' => chr(64 + $i), // A, B, C, etc.
                'number' => $i
            ]);
        }
    }

    public function test_admin_can_create_screening()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/admin/screenings', [
            'movie_id' => $this->movie->id,
            'room_id' => $this->room->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '18:30',
            'is_special' => false
        ]);

        $response->assertStatus(201);
    }

    public function test_create_screening_validation()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/admin/screenings', []);

        $response->assertStatus(400)
            ->assertJsonValidationErrors([
                'movie_id',
                'room_id',
                'date',
                'time',
                'is_special'
            ]);
    }
    public function test_prevent_time_conflict()
    {
        Sanctum::actingAs(User::factory()->create());

        Screening::factory()->create([
            'room_id' => $this->room->id,
            'date' => $date = now()->addDay()->format('Y-m-d'),
            'time' => '18:30'
        ]);

        $response = $this->postJson('/api/admin/screenings', [
            'movie_id' => $this->movie->id,
            'room_id' => $this->room->id,
            'date' => $date,
            'time' => '18:30',
            'is_special' => false
        ]);

        $response->assertStatus(409);
    }

    //------------------ UPDATE ------------------
    public function test_admin_can_update_screening()
    {
        Sanctum::actingAs(User::factory()->create());
        $screening = Screening::factory()->create();

        $response = $this->putJson("/api/admin/screenings/{$screening->id}", [
            'time' => '20:00',
            'is_special' => true
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'time' => '20:00'
            ]);
    }

    //------------------ DELETE ------------------
    public function test_cant_delete_screening_with_tickets()
    {
        Sanctum::actingAs(User::factory()->create());
        $screening = Screening::factory()->create();
        Ticket::factory()->for($screening)->create();

        $response = $this->deleteJson("/api/admin/screenings/{$screening->id}");

        $response->assertStatus(400)
            ->assertJson(['message' => 'No se puede eliminar la proyección, ya se han comprado tickets para esa sesión.']);
    }

    public function test_admin_can_delete_screening()
    {
        Sanctum::actingAs(User::factory()->create());
        $screening = Screening::factory()->create();

        $response = $this->deleteJson("/api/admin/screenings/{$screening->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Proyección eliminada']);
    }

    //------------------ SHOW ------------------
    public function test_show_screening_with_seats()
    {
        $screening = Screening::factory()->create();

        $response = $this->getJson("/api/screenings/{$screening->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'movie',
                'screening',
                'room',
                'seats' => [
                    '*' => ['id', 'row', 'number', 'type', 'is_occupied']
                ]
            ]);
    }

    //------------------ GET SCHEDULED MOVIES ------------------
    public function test_get_scheduled_movies()
    {
        // 1. Crear una sola película
        $movie = Movie::factory()->create();

        // 2. Crear 3 screenings para la misma película
        Screening::factory()
            ->count(3)
            ->for($movie)
            ->create();

        $response = $this->getJson('/api/screenings/movies');

        $response->assertStatus(200)
            ->assertJsonCount(1); // Ahora sí habrá 1 película única
    }

    //------------------ INDEX CLIENT ------------------
    public function test_client_index_with_dates()
    {
        $start = now()->format('Y-m-d');
        $end = now()->addWeek()->format('Y-m-d');

        Screening::factory()->create(['date' => now()->addDay()]);

        $response = $this->getJson("/api/screenings?start_date=$start&end_date=$end");

        $response->assertStatus(200)
            ->assertJsonStructure([
                ['id', 'date', 'time', 'movie', 'room']
            ])
            ->assertJsonMissingPath('0.stats');
    }

    //------------------ INDEX ADMIN ------------------
    public function test_admin_index_with_stats()
    {
        Sanctum::actingAs(User::factory()->create());
        Screening::factory()->create();

        $response = $this->getJson('/api/admin/screenings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                ['id', 'date', 'time', 'movie', 'room', 'stats']
            ]);
    }

}