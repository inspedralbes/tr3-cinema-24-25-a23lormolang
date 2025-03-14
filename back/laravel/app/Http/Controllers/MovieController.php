<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{

    // Busca la pelicula a omdb
    public function omdbSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $searchTerm = $request->input('query');
        $response = Http::get('http://www.omdbapi.com/', [
            'apikey' => config('services.omdb.key'),
            's' => $searchTerm,
            'type' => 'movie'
        ]);

        return response()->json($response->json()['Search'] ?? []);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'imdb_id' => 'required|string'
        ]);

        $movie = Movie::where('imdb_id', $validated['imdb_id'])->first();
        if ($movie) {
            return response()->json($movie, 201);
        }

        // Obtener datos completos de OMDB
        $omdbResponse = Http::get('http://www.omdbapi.com/', [
            'apikey' => env('OMDB_API_KEY'),
            'i' => $validated['imdb_id'],
            'plot' => 'full'
        ]);

        if (!$omdbResponse->successful() || $omdbResponse->json('Response') === 'False') {
            return response()->json(['error' => 'Película no encontrada en OMDB'], 404);
        }

        $movieData = $omdbResponse->json();

        // Convertir runtime a minutos
        $duration = (int) filter_var($movieData['Runtime'], FILTER_SANITIZE_NUMBER_INT);

        // Crear película
        $movie = Movie::create([
            'imdb_id' => $movieData['imdbID'],
            'title' => $movieData['Title'],
            'description' => $movieData['Plot'],
            'duration' => $duration,
            'poster_url' => $movieData['Poster'] !== 'N/A' ? $movieData['Poster'] : null,
            'year' => $movieData['Year'],
            'genre' => $movieData['Genre'],
            'director' => $movieData['Director'],
            'actors' => $movieData['Actors'],
            'awards' => $movieData['Awards'] !== 'N/A' ? $movieData['Awards'] : null,
            'imdb_rating' => $movieData['imdbRating'] !== 'N/A' ? $movieData['imdbRating'] : null,
            'box_office' => $movieData['BoxOffice'] !== 'N/A' ? $movieData['BoxOffice'] : null,
        ]);

        return response()->json($movie, 201);
    }
}