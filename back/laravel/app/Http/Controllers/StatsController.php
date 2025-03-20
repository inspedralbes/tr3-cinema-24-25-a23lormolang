<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Screening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function sales(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'sometimes|in:week,month,year'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Periodo no válido'], 400);
        }

        $period = $request->input('period', 'week');

        try {
            $data = match ($period) {
                'week' => $this->getWeeklyStats(),
                'month' => $this->getMonthlyStats(),
                'year' => $this->getYearlyStats(),
            };

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getWeeklyStats()
    {
        $sales = Ticket::join('screenings', 'tickets.screening_id', '=', 'screenings.id')
            ->whereBetween('screenings.date', [Carbon::today(), Carbon::today()->addWeek()])
            ->selectRaw('screenings.date as date, CAST(SUM(tickets.price) AS DECIMAL(10,2)) as total')
            ->groupBy('screenings.date')
            ->orderBy('screenings.date')
            ->get();

        return $this->formatResponse($sales);
    }

    private function getMonthlyStats()
    {
        $sales = Ticket::join('screenings', 'tickets.screening_id', '=', 'screenings.id')
            ->whereMonth('screenings.date', Carbon::now()->month)
            ->selectRaw('screenings.date as date, CAST(SUM(tickets.price) AS DECIMAL(10,2)) as total')
            ->groupBy('screenings.date')
            ->orderBy('screenings.date')
            ->get();

        return $this->formatResponse($sales);
    }

    private function getYearlyStats()
    {
        $sales = Ticket::join('screenings', 'tickets.screening_id', '=', 'screenings.id')
            ->whereYear('screenings.date', Carbon::now()->year)
            ->selectRaw('MONTH(screenings.date) as month, CAST(SUM(tickets.price) AS DECIMAL(10,2)) as total')
            ->groupBy(DB::raw('MONTH(screenings.date)'))
            ->orderBy('month')
            ->get();

        return [
            'labels' => $sales->pluck('month')->map(fn($m) => $this->getMonthName($m)),
            'data' => $sales->pluck('total')->map(fn($v) => (float)$v),
            'average' => (float)$sales->avg('total')
        ];
    }

    private function formatResponse($sales)
    {
        return [
            'labels' => $sales->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m/Y')),
            'data' => $sales->pluck('total')->map(fn($v) => (float)$v),
            'average' => (float)$sales->avg('total')
        ];
    }

    private function getMonthName($monthNumber)
    {
        return Carbon::create()->month($monthNumber)->locale('ca_ES')->monthName;
    }
}