<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScreeningController extends Controller
{
    public function index()
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

    public function show(Screening $screening)
    {
        $screening->load([
            'movie',
            'seats' => function ($query) {
                $query->orderBy('row')
                    ->orderBy('number');
            }
        ]);

        return response()->json([
            'movie' => $screening->movie,
            'screening' => $screening->only(['id', 'date', 'time', 'is_special', 'is_vip_active']),
            'seats' => $screening->seats->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                    'type' => $seat->type,
                    'is_occupied' => $seat->is_occupied
                ];
            })
        ]);
    }
}