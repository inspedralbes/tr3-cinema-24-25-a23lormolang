<?php

namespace Tests\Feature;

use App\Models\{User, Screening, Seat, Reservation, Room, Ticket, PurchaseToken};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use App\Mail\BuyTicketsEmail;
use App\Mail\PurchaseAccessLink;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Crear sala con asientos
        $room = Room::factory()
            ->has(Seat::factory()->count(10))
            ->create();

        // Crear proyección para la sala
        $this->screening = Screening::factory()
            ->for($room)
            ->create();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function can_create_reservation()
    {
        $seats = $this->screening->room->seats->take(2)->pluck('id');

        $response = $this->postJson('/api/reservations', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'screening_id' => $this->screening->id,
            'seats' => $seats
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'screening_id',
                'tickets' => [['id', 'seat_id', 'price']]
            ]);

        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseCount('tickets', 2);
        Mail::assertSent(BuyTicketsEmail::class);
    }

    #[Test]
    public function validation_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/reservations', [
            'name' => '',
            'email' => 'invalid-email',
            'screening_id' => 999,
            'seats' => []
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'screening_id',
                'seats'
            ]);
    }

    #[Test]
    public function cant_create_duplicate_reservation()
    {
        $user = User::factory()->create();
        $seats = $this->screening->room->seats->take(2)->pluck('id');

        // Primera reserva
        $this->postJson('/api/reservations', [
            'name' => $user->name,
            'email' => $user->email,
            'screening_id' => $this->screening->id,
            'seats' => $seats
        ]);

        // Segunda reserva misma sesión
        $response = $this->postJson('/api/reservations', [
            'name' => $user->name,
            'email' => $user->email,
            'screening_id' => $this->screening->id,
            'seats' => $seats
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Ya tienes una reserva para esta sesión']);
    }

    #[Test]
    public function cant_reserve_occupied_seats()
    {
        $seat = $this->screening->room->seats->first();

        // Ocupar butaca
        Ticket::factory()->create([
            'screening_id' => $this->screening->id,
            'seat_id' => $seat->id
        ]);

        $response = $this->postJson('/api/reservations', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'screening_id' => $this->screening->id,
            'seats' => [$seat->id]
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Butacas ocupadas: ' . $seat->id]);
    }

    #[Test]
    public function can_generate_access_link()
    {
        $response = $this->postJson('/api/reservations/access-link', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Enlace de acceso enviado a tu correo']);

        $this->assertDatabaseCount('purchase_tokens', 1);
        Mail::assertSent(PurchaseAccessLink::class);
    }

    #[Test]
    public function can_retrieve_purchases_with_valid_token()
    {
        $token = PurchaseToken::create([
            'email' => 'john@example.com',
            'token' => 'test-token',
            'expires_at' => Carbon::now()->addDay()
        ]);

        $response = $this->getJson('/api/reservations/purchases/test-token');

        $response->assertStatus(200);
        $this->assertDatabaseCount('purchase_tokens', 0);
    }

    #[Test]
    public function cant_retrieve_purchases_with_expired_token()
    {
        PurchaseToken::create([
            'email' => 'john@example.com',
            'token' => 'expired-token',
            'expires_at' => Carbon::now()->subDay()
        ]);

        $response = $this->getJson('/api/reservations/purchases/expired-token');

        $response->assertStatus(404);
    }
}