<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ScreeningController extends Controller
{
    public function nextScreens()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $screenings = Screening::with('movie')
            ->whereDate('date', $today)
            ->orWhereDate('date', $tomorrow)
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        return response()->json([
            'today' => $screenings->get($today->format('Y-m-d'), []),
            'tomorrow' => $screenings->get($tomorrow->format('Y-m-d'), [])
        ]);
    }

    // Panel Admin: Obtener todas las proyecciones en un rango de fechas
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $screenings = Screening::with(['movie', 'seats'])
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

    // Crear nueva proyección
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,id',
            'date' => 'required|date',
            'time' => 'required|in:16:00,18:00,20:00',
            'total_seats' => 'required|integer|min:1',
            'vip_seats' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $screening = Screening::create($request->all());
        $this->generateSeats($screening); // Generar butacas automáticamente

        return response()->json($this->formatScreeningReport($screening), 201);
    }

    // Actualizar proyección
    public function update(Request $request, Screening $screening)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'exists:movies,id',
            'date' => 'date',
            'time' => 'in:16:00,18:00,20:00',
            'total_seats' => 'integer|min:1',
            'vip_seats' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $screening->update($request->all());
        return response()->json($this->formatScreeningReport($screening));
    }

    // Eliminar proyección
    public function destroy(Screening $screening)
    {
        $screening->delete();
        return response()->json(['message' => 'Proyección eliminada']);
    }

    // Generar informe de ocupación/recaudación
    private function formatScreeningReport(Screening $screening)
    {
        $occupiedSeats = $screening->seats()->where('is_occupied', true)->count();
        $vipOccupied = $screening->seats()->where('type', 'vip')->where('is_occupied', true)->count();
        $normalOccupied = $occupiedSeats - $vipOccupied;

        return [
            'id' => $screening->id,
            'date' => $screening->date,
            'time' => $screening->time,
            'movie' => $screening->movie,
            'total_seats' => $screening->total_seats,
            'occupied_seats' => $occupiedSeats,
            'vip_seats' => $screening->vip_seats,
            'vip_occupied' => $vipOccupied,
            'normal_occupied' => $normalOccupied,
            'revenue' => $screening->seats->sum(function ($seat) {
                return $seat->is_occupied ? ($seat->type === 'vip' ? 8 : 6) : 0;
            }),
        ];
    }

    // Generar butacas automáticamente
    private function generateSeats(Screening $screening)
    {
        $rows = range('A', 'L');
        $number = 1;
        $seats = [];

        foreach ($rows as $row) {
            for ($i = 1; $i <= 10; $i++) {
                $seats[] = [
                    'screening_id' => $screening->id,
                    'row' => $row,
                    'number' => $i,
                    'type' => ($row >= 'F' && $i <= $screening->vip_seats) ? 'vip' : 'normal',
                    'is_occupied' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        Seat::insert($seats);
    }
}