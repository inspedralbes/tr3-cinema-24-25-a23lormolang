<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // public function test_admin_can_store_movie()
    // {
    //     // arrange
    //     $payload = ['imdb_id' => 'tt0123456'];
    //     // act
    //     $response = $this->postJson('/api/movies', $payload);
    //     // assert
    //     $response->assertStatus(201);
    // }

    // public function test_can_search_omdb()
    // {
    //     // arrange
    //     // act
    //     $response = $this->getJson('/api/omdb/search?query=matrix');
    //     // assert
    //     $response->assertStatus(200);
    // }

    // public function test_can_show_movie()
    // {
    //     // arrange
    //     // act
    //     $response = $this->getJson('/api/movies/1');
    //     // assert
    //     $response->assertStatus(200);
    // }
}
