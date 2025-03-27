<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\{User, Room, Seat, Screening, Ticket};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    #[Test]
    public function test_can_get_all_rooms()
    {
        Room::factory()->count(3)->create();

        $response = $this->getJson('/api/rooms');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    #[Test]
    public function test_can_get_single_room_with_details()
    {
        $room = Room::factory()
            ->has(Seat::factory()->count(5))
            ->create();

        $response = $this->getJson("/api/rooms/{$room->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'seat_map' => [
                    '*' => [
                        '*' => ['id', 'row', 'number', 'type']
                    ]
                ]
            ]);
    }

    /** @test */
    // 
    // public function test_can_check_room_availability()
    // {
    //     $room = Room::factory()
    //         ->has(Seat::factory()->count(5))
    //         ->create();

    //     $screening = Screening::factory()->create(['room_id' => $room->id]);
    //     $occupiedSeats = $room->seats->take(2);

    //     foreach ($occupiedSeats as $seat) {
    //         Ticket::factory()->create([
    //             'screening_id' => $screening->id,
    //             'seat_id' => $seat->id
    //         ]);
    //     }

    //     $response = $this->postJson('/api/rooms/availability', [
    //         'room_id' => $room->id,
    //         'screening_id' => $screening->id
    //     ]);

    //     $response->assertStatus(200)
    //         ->assertJsonCount(5, 'seats');
    // }

    #[Test]
    public function test_availability_for_non_existent_screening()
    {
        $room = Room::factory()->create();

        $response = $this->postJson("/api/rooms/{$room->id}/availability/999");

        $response->assertStatus(404);
    }
}