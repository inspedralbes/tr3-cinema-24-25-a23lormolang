<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Movie, Screening, Seat, User, Reservation, Ticket};
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Limpiar datos existentes
        $this->truncateTables();

        // Crear películas
        $movies = [
            [
                'title' => 'Avengers: Endgame',
                'description' => 'Los Vengadores se reúnen para derrotar a Thanos.',
                'duration' => 181,
                'poster_url' => 'https://preview.redd.it/esea05pj84o21.jpg?width=640&crop=smart&auto=webp&s=d00beefa8e27448e4ddd422eb3500ae069adf0f1'
            ],
            [
                'title' => 'Spider-Man: No Way Home',
                'description' => 'Spider-Man viaja al multiverso.',
                'duration' => 148,
                'poster_url' => 'https://example.com/posters/spiderman.jpg'
            ]
        ];

        $createdMovies = [];
        foreach ($movies as $movie) {
            $createdMovies[] = Movie::create($movie);
        }

        // Crear sesiones
        $screenings = [
            [
                'movie_id' => $createdMovies[0]->id,
                'date' => Carbon::today(),
                'time' => '16:00',
                'is_special' => false,
                'is_vip_active' => true
            ],
            // [
            //     'movie_id' => $createdMovies[1]->id,
            //     'date' => Carbon::today(),
            //     'time' => '20:00',
            //     'is_special' => true,
            //     'is_vip_active' => false
            // ],
            [
                'movie_id' => $createdMovies[0]->id,
                'date' => Carbon::tomorrow(),
                'time' => '18:00',
                'is_special' => false,
                'is_vip_active' => true
            ]
        ];

        foreach ($screenings as $screeningData) {
            $screening = Screening::create($screeningData);

            // Crear butacas
            $rows = range('A', 'L');
            foreach ($rows as $row) {
                for ($number = 1; $number <= 10; $number++) {
                    $type = ($row === 'F' && $screening->is_vip_active) ? 'vip' : 'normal';

                    Seat::create([
                        'screening_id' => $screening->id,
                        'row' => $row,
                        'number' => $number,
                        'type' => $type,
                        'is_occupied' => $this->randomOccupied()
                    ]);
                }
            }
        }

        // Crear usuarios de prueba
        $user1 = User::create([
            'name' => 'Juan',
            'email' => 'juan@example.com',
        ]);

        $user2 = User::create([
            'name' => 'María',
            'email' => 'maria@example.com',
        ]);

        $user3 = User::create([
            'name' => 'Lorenzo',
            'email' => 'lorenzo@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        // Crear reservas de prueba
        $screening = Screening::first();
        $seats = $screening->seats()->where('is_occupied', false)->take(3)->get();

        $reservation = Reservation::create([
            'user_id' => $user1->id,
            'screening_id' => $screening->id
        ]);

        foreach ($seats as $seat) {
            Ticket::create([
                'reservation_id' => $reservation->id,
                'seat_id' => $seat->id,
                'price' => $this->calculatePrice($seat, $screening)
            ]);

            $seat->update(['is_occupied' => true]);
        }
    }

    private function truncateTables()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ticket::truncate();
        Reservation::truncate();
        Seat::truncate();
        Screening::truncate();
        Movie::truncate();
        User::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function randomOccupied()
    {
        return rand(1, 10) > 7; // 30% de probabilidad de estar ocupada
    }

    private function calculatePrice($seat, $screening)
    {
        if ($screening->is_special) {
            return $seat->type === 'vip' ? 6.00 : 4.00;
        }
        return $seat->type === 'vip' ? 8.00 : 6.00;
    }
}