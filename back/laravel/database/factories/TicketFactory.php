<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'price' => $this->faker->randomFloat(2, 5, 20),
            'payment_reference' => $this->faker->uuid,
        ];
    }
}