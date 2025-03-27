<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // public function test_user_can_store_reservation()
    // {
    //     // arrange
    //     $payload = [
    //         'name' => 'John Doe',
    //         'email' => 'john@example.com',
    //         'screening_id' => 1,
    //         'seats' => [1, 2]
    //     ];
    //     // act
    //     $response = $this->postJson('/api/reservations', $payload);
    //     // assert
    //     $response->assertStatus(200);
    // }

    // public function test_user_can_generate_access_link()
    // {
    //     // arrange
    //     $payload = ['email' => 'john@example.com'];
    //     // act
    //     $response = $this->postJson('/api/reservations/access-link', $payload);
    //     // assert
    //     $response->assertStatus(200);
    // }

    // public function test_can_get_purchases_by_valid_token()
    // {
    //     // arrange
    //     // act
    //     // assume token is "test-token"
    //     $response = $this->getJson('/api/reservations/purchases/test-token');
    //     // assert
    //     $response->assertStatus(200);
    // }
}
