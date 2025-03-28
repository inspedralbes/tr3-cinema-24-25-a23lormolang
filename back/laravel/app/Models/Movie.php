<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id',
        'title',
        'description',
        'duration',
        'poster_url',
        'year',
        'genre',
        'director',
        'actors',
        'awards',
        'imdb_rating',
        'box_office'
    ];

    protected $casts = [
        'year' => 'integer',
        'duration' => 'integer',
        'imdb_rating' => 'decimal:1'
    ];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}