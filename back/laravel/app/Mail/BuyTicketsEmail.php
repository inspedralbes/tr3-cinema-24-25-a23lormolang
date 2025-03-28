<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class BuyTicketsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $user;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
        $this->user = $reservation->user;
    }

    public function build()
    {
        return $this->subject('🎟️ Gràcies per la teva reserva')
            ->view('emails.buytickets');
    }
}