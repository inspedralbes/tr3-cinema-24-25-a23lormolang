<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Reservation;
use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
{
    return [
        'reservation_id' => Reservation::factory(),
        'screening_id' => Screening::factory(),
        'seat_id' => Seat::factory(),
        'price' => $this->faker->randomFloat(2, 5, 15)
    ];
}
}