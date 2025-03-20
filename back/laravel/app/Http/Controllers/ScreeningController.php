<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScreeningController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $screenings = Screening::with(['movie', 'room'])
            ->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ])
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(function ($screening) {
                return $this->formatScreeningReport($screening);
            });

        return response()->json($screenings);
    }

    public function show(Screening $screening)
    {
        $screening->load(['movie', 'room.seats', 'tickets']);

        return response()->json([
            'movie' => $screening->movie->only(['id', 'title']),
            'screening' => $screening->only(['id', 'date', 'time', 'is_special', 'room_id']),
            'room' => $screening->room->only(['id', 'name', 'has_vip']),
            'seats' => $screening->room->seats->map(function ($seat) use ($screening) {
                return [
                    'id' => $seat->id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                    'type' => $seat->type,
                    'is_occupied' => $screening->tickets->contains('seat_id', $seat->id)
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'time' => [
                'required',
                'regex:/^([01]\d|2[0-3]):(00|30)$/'
            ],
            'is_special' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Verificar disponibilidad de la sala
        $existing = Screening::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'La sala ya está ocupada en este horario'], 409);
        }

        $screening = Screening::create($request->all());

        return response()->json($this->formatScreeningReport($screening), 201);
    }

    public function update(Request $request, Screening $screening)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'exists:movies,id',
            'room_id' => 'exists:rooms,id',
            'date' => 'date',
            'time' => [
                'required',
                'regex:/^([01]\d|2[0-3]):(00|30)$/'
            ],
            'is_special' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $screening->update($request->all());
        return response()->json($this->formatScreeningReport($screening));
    }

    public function destroy(Screening $screening)
    {
        // Comprobamos si hay tickets vendidos para la proyección
        if ($screening->tickets()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la proyección, ya se han comprado tickets para esa sesión.'
            ], 400);
        }
        
        $screening->delete();
        return response()->json(['message' => 'Proyección eliminada']);
    }

    private function formatScreeningReport(Screening $screening)
    {
        $occupied = $screening->tickets()->count();
        $vipOccupied = $screening->tickets()
            ->whereHas('seat', fn($q) => $q->where('type', 'vip'))
            ->count();

        return [
            'id' => $screening->id,
            'date' => $screening->date,
            'time' => $screening->time,
            'movie' => $screening->movie->only(['id', 'title', 'description', 'duration', 'genre', 'imdb_rating', 'poster_url']),
            'room' => $screening->room->only(['id', 'name', 'has_vip', 'total_seats', 'vip_seats']),
            'stats' => [
                'normal_seats' => $screening->room->total_seats - $screening->room->vip_seats,
                'occupied' => $occupied,
                'vip_occupied' => $vipOccupied,
                'normal_occupied' => $occupied - $vipOccupied,
                'revenue' => $screening->tickets->sum('price')
            ]
        ];
    }

    public function getScheduledMovies(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $movies = Screening::query()
            ->when($request->start_date, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($request->end_date, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('movie')
            ->get()
            ->pluck('movie')
            ->unique('id')
            ->values();

        return response()->json($movies);
    }
}