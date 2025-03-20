<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Screening;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all()->map(function ($room) {
            return $this->formatRoom($room);
        });

        return response()->json($rooms);
    }

    public function show(Room $room)
    {
        return response()->json($this->formatRoom($room, true));
    }

    public function getAvailability(Room $room, Screening $screening)
    {
        $occupiedSeats = $screening->tickets()->pluck('seat_id');

        $seats = $room->seats->map(function ($seat) use ($occupiedSeats) {
            return [
                'id' => $seat->id,
                'row' => $seat->row,
                'number' => $seat->number,
                'type' => $seat->type,
                'available' => !$occupiedSeats->contains($seat->id)
            ];
        });

        return response()->json([
            'room' => $this->formatRoom($room),
            'screening' => $screening->only(['id', 'date', 'time']),
            'seats' => $seats
        ]);
    }

    private function formatRoom(Room $room, $detailed = false)
    {
        $data = [
            'id' => $room->id,
            'name' => $room->name,
            'has_vip' => $room->has_vip,
            'total_seats' => $room->total_seats,
            'vip_seats' => $room->vip_seats
        ];

        if ($detailed) {
            $data['seat_map'] = $room->seats->groupBy('row')->map(function ($seats) {
                return $seats->sortBy('number')->values();
            });
        }

        return $data;
    }
}