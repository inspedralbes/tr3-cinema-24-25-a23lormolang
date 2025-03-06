<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\Screening;
use App\Models\Ticket;

class Seat extends Model
{
    protected $fillable = [
        'is_occupied',
    ];



    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}