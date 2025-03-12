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
        // Películas actualizadas con datos reales de OMDB
        $movies = [
            [
                'imdb_id' => 'tt0068646',
                'title' => 'The Godfather',
                'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                'duration' => 175,
                'poster_url' => 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg',
                'year' => 1972,
                'genre' => 'Crime, Drama',
                'director' => 'Francis Ford Coppola',
                'actors' => 'Marlon Brando, Al Pacino, James Caan',
                'awards' => 'Won 3 Oscars. 32 wins & 30 nominations total',
                'imdb_rating' => 9.2,
                'box_office' => '$136,381,073'
            ],
            [
                'imdb_id' => 'tt1375666',
                'title' => 'Inception',
                'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
                'duration' => 148,
                'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_SX300.jpg',
                'year' => 2010,
                'genre' => 'Action, Adventure, Sci-Fi',
                'director' => 'Christopher Nolan',
                'actors' => 'Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page',
                'awards' => 'Won 4 Oscars. 159 wins & 220 nominations total',
                'imdb_rating' => 8.8,
                'box_office' => '$292,587,330'
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