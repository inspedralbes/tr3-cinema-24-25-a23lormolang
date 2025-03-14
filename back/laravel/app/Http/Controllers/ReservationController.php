<?php

namespace App\Http\Controllers;

use App\Models\{User, Screening, Reservation, Seat, Ticket};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'screening_id' => 'required|exists:screenings,id',
            'seats' => 'required|array|min:1|max:10',
            'seats.*' => 'exists:seats,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();

            // Buscar o crear usuario
            $user = User::firstOrCreate(
                ['email' => $request->email],
                $request->only('name')
            );

            // Verificar reservas existentes
            if ($user->reservations()->where('screening_id', $request->screening_id)->exists()) {
                throw new \Exception('Ya tienes una reserva para esta sesión');
            }

            // Verificar butacas
            $seats = Seat::where('screening_id', $request->screening_id)
                ->whereIn('id', $request->seats)
                ->get();

            if ($seats->count() !== count($request->seats)) {
                throw new \Exception('Alguna butaca no es válida');
            }

            if ($seats->where('is_occupied', true)->count() > 0) {
                throw new \Exception('Alguna butaca ya está ocupada');
            }

            // Crear reserva
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'screening_id' => $request->screening_id
            ]);

            // Crear tickets y marcar butacas
            foreach ($seats as $seat) {
                Ticket::create([
                    'reservation_id' => $reservation->id,
                    'seat_id' => $seat->id,
                    'price' => $this->calculatePrice($seat, $reservation->screening)
                ]);

                $seat->update(['is_occupied' => true]);
            }

            DB::commit();

            return response()->json($reservation->load('tickets.seat'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->firstOrFail();

        $reservations = Reservation::with(['screening.movie', 'tickets.seat'])
            ->where('user_id', $user->id)
            ->whereHas('screening')
            ->get();

        return response()->json($reservations);
    }

    private function calculatePrice(Seat $seat, Screening $screening)
    {
        if ($screening->is_special) {
            return $seat->type === 'vip' ? 6.00 : 4.00;
        }
        return $seat->type === 'vip' ? 8.00 : 6.00;
    }
}