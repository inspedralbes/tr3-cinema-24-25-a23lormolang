<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Movie;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    protected $fillable = [
        'movie_id',
        'date',
        'time',
        'total_seats',
        'vip_seats',
        'is_special',
        'is_vip_active'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}