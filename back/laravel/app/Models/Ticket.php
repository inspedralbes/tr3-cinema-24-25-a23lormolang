<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Seat;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'reservation_id',
        'seat_id',
        'price',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
