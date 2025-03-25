<?php

namespace App\Http\Controllers;

use App\Models\{User, Screening, Reservation, Seat, Ticket};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\BuyTicketsEmail;

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

            // Verificar reservas existentes del usuario para esta sesión
            if ($user->reservations()->where('screening_id', $request->screening_id)->exists()) {
                throw new \Exception('Ya tienes una reserva para esta sesión');
            }

            // Obtener la proyección con sus tickets
            $screening = Screening::with('tickets')->findOrFail($request->screening_id);

            // Verificar que las butacas pertenecen a la sala de la proyección
            $validSeatIds = $screening->room->seats->pluck('id')->toArray();
            $invalidSeats = array_diff($request->seats, $validSeatIds);

            if (!empty($invalidSeats)) {
                throw new \Exception('Alguna butaca no pertenece a esta sala');
            }

            // Verificar butacas ya ocupadas (vía tickets)
            $occupiedSeats = $screening->tickets->whereIn('seat_id', $request->seats)->pluck('seat_id');

            if ($occupiedSeats->isNotEmpty()) {
                throw new \Exception('Butacas ocupadas: ' . $occupiedSeats->implode(', '));
            }

            // Crear la reserva
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'screening_id' => $screening->id
            ]);

            // Crear tickets
            foreach ($request->seats as $seatId) {
                Ticket::create([
                    'reservation_id' => $reservation->id,
                    'screening_id' => $screening->id, 
                    'seat_id' => $seatId,
                    'price' => $this->calculatePrice(Seat::find($seatId), $screening)
                ]);
            }

            DB::commit();

            Mail::to($user)->send(new BuyTicketsEmail($reservation));

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