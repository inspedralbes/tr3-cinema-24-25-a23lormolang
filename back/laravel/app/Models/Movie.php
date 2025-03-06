<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Models\Screening;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duration',
        'poster_url',
    ];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}