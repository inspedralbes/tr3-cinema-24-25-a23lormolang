<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Movie, Screening, Seat, User, Reservation, Ticket, Room};
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Limpiar datos existentes
        $this->truncateTables();

        // Crear salas
        $rooms = [
            [
                'name' => 'Sala Estàndar',
                'has_vip' => false,
                'total_seats' => 120,
                'vip_seats' => 0
            ],
            [
                'name' => 'Sala Premium',
                'has_vip' => true,
                'total_seats' => 120,
                'vip_seats' => 20
            ],
            [
                'name' => 'Sala Deluxe',
                'has_vip' => true,
                'total_seats' => 120,
                'vip_seats' => 15
            ],
            [
                'name' => 'Sala Boutique',
                'has_vip' => true,
                'total_seats' => 120,
                'vip_seats' => 10
            ],
            [
                'name' => 'Sala Èpica',
                'has_vip' => true,
                'total_seats' => 120,
                'vip_seats' => 8
            ]
        ];

        foreach ($rooms as $roomData) {
            $room = Room::create($roomData);
            $this->generateSeats($room);
        }

        // Crear películas
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
                'room_id' => Room::where('name', 'Sala Premium')->first()->id,
                'date' => Carbon::today(),
                'time' => '16:00',
                'is_special' => false
            ],
            [
                'movie_id' => $createdMovies[1]->id,
                'room_id' => Room::where('name', 'Sala Deluxe')->first()->id,
                'date' => Carbon::today(),
                'time' => '18:00',
                'is_special' => false
            ],
            [
                'movie_id' => $createdMovies[0]->id,
                'room_id' => Room::where('name', 'Sala Deluxe')->first()->id,
                'date' => Carbon::tomorrow(),
                'time' => '18:00',
                'is_special' => false
            ]
        ];

        foreach ($screenings as $screeningData) {
            Screening::create($screeningData);
        }

        // Crear usuarios de prueba
        $users = [
            [
                'name' => 'Juan',
                'email' => 'juan@example.com',
            ],
            [
                'name' => 'María',
                'email' => 'maria@example.com',
            ],
            [
                'name' => 'Lorenzo',
                'email' => 'lorenzo@gmail.com',
                'password' => bcrypt('pirineus')
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Crear reservas de prueba (modificado)
        $screening = Screening::first();
        $room = $screening->room;

        // Seleccionar 7 asientos aleatorios no ocupados para esta proyección
        $seats = $room->seats()
            ->whereDoesntHave('tickets', function ($query) use ($screening) {
                $query->where('screening_id', $screening->id);
            })
            ->inRandomOrder()
            ->take(7)
            ->get();

        $reservation = Reservation::create([
            'user_id' => User::first()->id,
            'screening_id' => $screening->id
        ]);

        foreach ($seats as $seat) {
            Ticket::create([
                'reservation_id' => $reservation->id,
                'screening_id' => $screening->id, // Nuevo campo
                'seat_id' => $seat->id,
                'price' => $this->calculatePrice($seat, $screening)
            ]);
        }

        // Crear reservas de prueba para la segunda película
        $secondScreening = Screening::find(2); // Obtener la segunda proyección
        $secondRoom = $secondScreening->room;

        // Seleccionar 7 asientos aleatorios no ocupados para esta proyección
        $secondSeats = $secondRoom->seats()
            ->whereDoesntHave('tickets', function ($query) use ($secondScreening) {
                $query->where('screening_id', $secondScreening->id);
            })
            ->inRandomOrder()
            ->take(7)
            ->get();

        // Crear reserva para el segundo usuario
        $secondReservation = Reservation::create([
            'user_id' => User::find(2)->id, // Segundo usuario
            'screening_id' => $secondScreening->id
        ]);

        // Crear tickets para los asientos seleccionados
        foreach ($secondSeats as $seat) {
            Ticket::create([
                'reservation_id' => $secondReservation->id,
                'screening_id' => $secondScreening->id,
                'seat_id' => $seat->id,
                'price' => $this->calculatePrice($seat, $secondScreening)
            ]);
        }

        // Crear reservas de prueba para la tercer película
        $thirdScreening = Screening::find(3); 
        $secondRoom = $thirdScreening->room;

        // Seleccionar 7 asientos aleatorios no ocupados para esta proyección
        $thirdSeats = $secondRoom->seats()
            ->whereDoesntHave('tickets', function ($query) use ($thirdScreening) {
                $query->where('screening_id', $thirdScreening->id);
            })
            ->inRandomOrder()
            ->take(7)
            ->get();

        // Crear reserva para el segundo usuario
        $thirdReservation = Reservation::create([
            'user_id' => User::find(2)->id, // Segundo usuario
            'screening_id' => $thirdScreening->id
        ]);
        
        // Crear tickets para los asientos seleccionados
        foreach ($thirdSeats as $seat) {
            Ticket::create([
                'reservation_id' => $thirdReservation->id,
                'screening_id' => $thirdScreening->id,
                'seat_id' => $seat->id,
                'price' => $this->calculatePrice($seat, $thirdScreening)
            ]);
        }

    }

    private function generateSeats(Room $room)
    {
        $rows = range('A', 'L');
        $seats = [];
        $vipCount = 0;

        foreach ($rows as $row) {
            for ($number = 1; $number <= 10; $number++) {
                $type = 'normal';

                if ($room->has_vip) {
                    // Distribuciones VIP diferentes por sala
                    switch ($room->name) {
                        case 'Sala Premium':
                            // VIP en filas F y G (primeros 10 asientos)
                            if (in_array($row, ['F', 'G']) && $number <= 10 && $vipCount < $room->vip_seats) {
                                $type = 'vip';
                                $vipCount++;
                            }
                            break;

                        case 'Sala Deluxe':
                            // VIP en filas E (8 asientos) y H (7 asientos)
                            if ((($row == 'E' && $number <= 8) || ($row == 'H' && $number <= 7)) && $vipCount < $room->vip_seats) {
                                $type = 'vip';
                                $vipCount++;
                            }
                            break;

                        case 'Sala Boutique':
                            // VIP en fila D completa
                            if ($row == 'D' && $vipCount < $room->vip_seats) {
                                $type = 'vip';
                                $vipCount++;
                            }
                            break;

                        case 'Sala Èpica':
                            // VIP dispersos en filas I y J
                            if ((in_array($row, ['I', 'J']) && $number % 2 == 0) && $vipCount < $room->vip_seats) {
                                $type = 'vip';
                                $vipCount++;
                            }
                            break;
                    }
                }

                $seats[] = [
                    'room_id' => $room->id,
                    'row' => $row,
                    'number' => $number,
                    'type' => $type,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        Seat::insert($seats);
    }

    private function truncateTables()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ticket::truncate();
        Reservation::truncate();
        Seat::truncate();
        Screening::truncate();
        Room::truncate();
        Movie::truncate();
        User::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function calculatePrice($seat, $screening)
    {
        $basePrice = $screening->is_special ? 4.00 : 6.00;
        return $seat->type === 'vip' ? $basePrice + 2.00 : $basePrice;
    }
}