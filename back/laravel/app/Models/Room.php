<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'has_vip', 'total_seats', 'vip_seats', 'seat_map'];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}