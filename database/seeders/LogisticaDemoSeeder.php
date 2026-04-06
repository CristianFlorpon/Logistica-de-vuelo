<?php

namespace Database\Seeders;

use App\Models\Aerolinea;
use App\Models\Aeropuerto;
use App\Models\Flota;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Ruta;
use App\Models\Vuelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LogisticaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = $this->seedAerolineas();
        $airports = $this->seedAeropuertos();
        $routes = $this->seedRutas($airports);
        $fleets = $this->seedFlotas($airlines);
        $flights = $this->seedVuelos($airlines, $routes, $fleets);
        $passengers = $this->seedPasajeros($flights);
        $this->seedReservas($flights, $passengers);
    }

    private function seedAerolineas(): array
    {
        $records = [
            ['nombre' => 'Boliviana de Aviacion', 'codigo' => 'BOA', 'pais' => 'Bolivia'],
            ['nombre' => 'LATAM Airlines', 'codigo' => 'LAT', 'pais' => 'Chile'],
            ['nombre' => 'Avianca', 'codigo' => 'AVI', 'pais' => 'Colombia'],
            ['nombre' => 'American Airlines', 'codigo' => 'AAL', 'pais' => 'Estados Unidos'],
            ['nombre' => 'Sky Airline', 'codigo' => 'SKY', 'pais' => 'Chile'],
            ['nombre' => 'Copa Airlines', 'codigo' => 'CPA', 'pais' => 'Panama'],
            ['nombre' => 'Gol Linhas Aereas', 'codigo' => 'GOL', 'pais' => 'Brasil'],
            ['nombre' => 'Aerolineas Argentinas', 'codigo' => 'ARG', 'pais' => 'Argentina'],
            ['nombre' => 'JetSMART', 'codigo' => 'JSM', 'pais' => 'Chile'],
            ['nombre' => 'Viva Aerobus', 'codigo' => 'VVA', 'pais' => 'Mexico'],
            ['nombre' => 'Iberia', 'codigo' => 'IBE', 'pais' => 'Espana'],
            ['nombre' => 'Air Europa', 'codigo' => 'AEA', 'pais' => 'Espana'],
            ['nombre' => 'Delta Air Lines', 'codigo' => 'DAL', 'pais' => 'Estados Unidos'],
            ['nombre' => 'United Airlines', 'codigo' => 'UAL', 'pais' => 'Estados Unidos'],
            ['nombre' => 'Emirates', 'codigo' => 'UAE', 'pais' => 'Emiratos Arabes Unidos'],
            ['nombre' => 'Qatar Airways', 'codigo' => 'QTR', 'pais' => 'Qatar'],
            ['nombre' => 'KLM', 'codigo' => 'KLM', 'pais' => 'Paises Bajos'],
            ['nombre' => 'Air France', 'codigo' => 'AFR', 'pais' => 'Francia'],
            ['nombre' => 'Lufthansa', 'codigo' => 'DLH', 'pais' => 'Alemania'],
            ['nombre' => 'Turkish Airlines', 'codigo' => 'THY', 'pais' => 'Turquia'],
        ];

        $collection = [];

        foreach ($records as $record) {
            $collection[] = Aerolinea::updateOrCreate(
                ['codigo' => $record['codigo']],
                $record
            );
        }

        return $collection;
    }

    private function seedAeropuertos(): array
    {
        $records = [
            ['nombre' => 'El Alto International Airport', 'codigo_iata' => 'LPB', 'ciudad' => 'La Paz', 'pais' => 'Bolivia', 'latitud' => -16.5133, 'longitud' => -68.1923],
            ['nombre' => 'Viru Viru International Airport', 'codigo_iata' => 'VVI', 'ciudad' => 'Santa Cruz', 'pais' => 'Bolivia', 'latitud' => -17.6448, 'longitud' => -63.1354],
            ['nombre' => 'Jorge Wilstermann International Airport', 'codigo_iata' => 'CBB', 'ciudad' => 'Cochabamba', 'pais' => 'Bolivia', 'latitud' => -17.4211, 'longitud' => -66.1771],
            ['nombre' => 'Jorge Chavez International Airport', 'codigo_iata' => 'LIM', 'ciudad' => 'Lima', 'pais' => 'Peru', 'latitud' => -12.0219, 'longitud' => -77.1143],
            ['nombre' => 'Arturo Merino Benitez Airport', 'codigo_iata' => 'SCL', 'ciudad' => 'Santiago', 'pais' => 'Chile', 'latitud' => -33.3930, 'longitud' => -70.7858],
            ['nombre' => 'Ezeiza International Airport', 'codigo_iata' => 'EZE', 'ciudad' => 'Buenos Aires', 'pais' => 'Argentina', 'latitud' => -34.8222, 'longitud' => -58.5358],
            ['nombre' => 'El Dorado International Airport', 'codigo_iata' => 'BOG', 'ciudad' => 'Bogota', 'pais' => 'Colombia', 'latitud' => 4.7016, 'longitud' => -74.1469],
            ['nombre' => 'Mariscal Sucre International Airport', 'codigo_iata' => 'UIO', 'ciudad' => 'Quito', 'pais' => 'Ecuador', 'latitud' => -0.1292, 'longitud' => -78.3575],
            ['nombre' => 'Silvio Pettirossi International Airport', 'codigo_iata' => 'ASU', 'ciudad' => 'Asuncion', 'pais' => 'Paraguay', 'latitud' => -25.2399, 'longitud' => -57.5191],
            ['nombre' => 'Carrasco International Airport', 'codigo_iata' => 'MVD', 'ciudad' => 'Montevideo', 'pais' => 'Uruguay', 'latitud' => -34.8384, 'longitud' => -56.0308],
            ['nombre' => 'Benito Juarez International Airport', 'codigo_iata' => 'MEX', 'ciudad' => 'Ciudad de Mexico', 'pais' => 'Mexico', 'latitud' => 19.4361, 'longitud' => -99.0719],
            ['nombre' => 'Tocumen International Airport', 'codigo_iata' => 'PTY', 'ciudad' => 'Panama', 'pais' => 'Panama', 'latitud' => 9.0714, 'longitud' => -79.3835],
            ['nombre' => 'Miami International Airport', 'codigo_iata' => 'MIA', 'ciudad' => 'Miami', 'pais' => 'Estados Unidos', 'latitud' => 25.7959, 'longitud' => -80.2871],
            ['nombre' => 'John F. Kennedy International Airport', 'codigo_iata' => 'JFK', 'ciudad' => 'Nueva York', 'pais' => 'Estados Unidos', 'latitud' => 40.6413, 'longitud' => -73.7781],
            ['nombre' => 'Adolfo Suarez Madrid-Barajas Airport', 'codigo_iata' => 'MAD', 'ciudad' => 'Madrid', 'pais' => 'Espana', 'latitud' => 40.4983, 'longitud' => -3.5676],
            ['nombre' => 'Charles de Gaulle Airport', 'codigo_iata' => 'CDG', 'ciudad' => 'Paris', 'pais' => 'Francia', 'latitud' => 49.0097, 'longitud' => 2.5479],
            ['nombre' => 'Amsterdam Schiphol Airport', 'codigo_iata' => 'AMS', 'ciudad' => 'Amsterdam', 'pais' => 'Paises Bajos', 'latitud' => 52.3105, 'longitud' => 4.7683],
            ['nombre' => 'Frankfurt Airport', 'codigo_iata' => 'FRA', 'ciudad' => 'Frankfurt', 'pais' => 'Alemania', 'latitud' => 50.0379, 'longitud' => 8.5622],
            ['nombre' => 'Hamad International Airport', 'codigo_iata' => 'DOH', 'ciudad' => 'Doha', 'pais' => 'Qatar', 'latitud' => 25.2731, 'longitud' => 51.6081],
            ['nombre' => 'Dubai International Airport', 'codigo_iata' => 'DXB', 'ciudad' => 'Dubai', 'pais' => 'Emiratos Arabes Unidos', 'latitud' => 25.2532, 'longitud' => 55.3657],
        ];

        $collection = [];

        foreach ($records as $record) {
            $collection[] = Aeropuerto::updateOrCreate(
                ['codigo_iata' => $record['codigo_iata']],
                $record
            );
        }

        return $collection;
    }

    private function seedRutas(array $airports): array
    {
        $collection = [];
        $total = count($airports);

        for ($i = 0; $i < 20; $i++) {
            $origin = $airports[$i % $total];
            $destination = $airports[($i + 5) % $total];
            $distance = $this->calculateDistance($origin, $destination);

            $collection[] = Ruta::updateOrCreate(
                ['codigo' => sprintf('RUTA-%03d', $i + 1)],
                [
                    'pais' => $origin->pais,
                    'origen_airport_id' => $origin->id,
                    'destino_airport_id' => $destination->id,
                    'distancia_km' => $distance,
                    'tiempo_estimado_min' => max(45, (int) round(($distance / 750) * 60)),
                ]
            );
        }

        return $collection;
    }

    private function seedFlotas(array $airlines): array
    {
        $models = [
            'Airbus A320neo',
            'Boeing 737-800',
            'Embraer E190',
            'Airbus A321',
            'Boeing 787-8',
            'Airbus A330-200',
            'Boeing 777-300ER',
            'ATR 72-600',
        ];

        $types = [
            'Corto alcance',
            'Medio alcance',
            'Regional',
            'Largo alcance',
        ];

        $collection = [];
        $total = count($airlines);

        for ($i = 0; $i < 20; $i++) {
            $airline = $airlines[$i % $total];

            $collection[] = Flota::updateOrCreate(
                ['matricula' => sprintf('CP-%03d', $i + 101)],
                [
                    'aerolinea_id' => $airline->id,
                    'modelo' => $models[$i % count($models)],
                    'capacidad' => 120 + ($i * 8),
                    'alcance_km' => 1800 + ($i * 320),
                    'tipo' => $types[$i % count($types)],
                ]
            );
        }

        return $collection;
    }

    private function seedVuelos(array $airlines, array $routes, array $fleets): array
    {
        $collection = [];

        for ($i = 0; $i < 20; $i++) {
            $route = $routes[$i];
            $fleet = $fleets[$i];
            $airline = $airlines[$i];
            $departure = Carbon::now()->addHours(($i + 1) * 4);
            $arrival = (clone $departure)->addMinutes($route->tiempo_estimado_min);

            $collection[] = Vuelo::updateOrCreate(
                ['codigo' => sprintf('FL-%03d', $i + 1)],
                [
                    'origen' => sprintf('%s (%s)', $route->origen->ciudad, $route->origen->codigo_iata),
                    'destino' => sprintf('%s (%s)', $route->destino->ciudad, $route->destino->codigo_iata),
                    'asientos' => $fleet->capacidad,
                    'estado' => Vuelo::ESTADOS[$i % count(Vuelo::ESTADOS)],
                    'aerolinea_id' => $airline->id,
                    'ruta_id' => $route->id,
                    'flota_id' => $fleet->id,
                    'salida_programada' => $departure,
                    'llegada_programada' => $arrival,
                ]
            );
        }

        return $collection;
    }

    private function seedPasajeros(array $flights): array
    {
        $names = [
            ['nombre' => 'Carlos', 'apellido' => 'Mendoza'],
            ['nombre' => 'Lucia', 'apellido' => 'Fernandez'],
            ['nombre' => 'Diego', 'apellido' => 'Rojas'],
            ['nombre' => 'Maria', 'apellido' => 'Suarez'],
            ['nombre' => 'Javier', 'apellido' => 'Salazar'],
            ['nombre' => 'Ana', 'apellido' => 'Paredes'],
            ['nombre' => 'Miguel', 'apellido' => 'Lopez'],
            ['nombre' => 'Paola', 'apellido' => 'Guzman'],
            ['nombre' => 'Andres', 'apellido' => 'Torrez'],
            ['nombre' => 'Sofia', 'apellido' => 'Vargas'],
            ['nombre' => 'Camila', 'apellido' => 'Molina'],
            ['nombre' => 'Pedro', 'apellido' => 'Arias'],
            ['nombre' => 'Valeria', 'apellido' => 'Quispe'],
            ['nombre' => 'Jose', 'apellido' => 'Rivera'],
            ['nombre' => 'Tatiana', 'apellido' => 'Peña'],
            ['nombre' => 'Marco', 'apellido' => 'Nina'],
            ['nombre' => 'Elena', 'apellido' => 'Flores'],
            ['nombre' => 'Fernando', 'apellido' => 'Navarro'],
            ['nombre' => 'Gabriela', 'apellido' => 'Castro'],
            ['nombre' => 'Daniel', 'apellido' => 'Ortiz'],
        ];

        $collection = [];

        for ($i = 0; $i < 20; $i++) {
            $person = $names[$i];
            $flight = $flights[$i];

            $collection[] = Pasajero::updateOrCreate(
                ['documento' => sprintf('DOC-%05d', $i + 1)],
                [
                    'nombre' => $person['nombre'],
                    'apellido' => $person['apellido'],
                    'email' => sprintf('pasajero%02d@demo.com', $i + 1),
                    'telefono' => sprintf('700%05d', $i + 101),
                    'vuelo_id' => $flight->id,
                ]
            );
        }

        return $collection;
    }

    private function seedReservas(array $flights, array $passengers): void
    {
        for ($i = 0; $i < 20; $i++) {
            Reserva::updateOrCreate(
                ['codigo' => sprintf('RSV-%04d', $i + 1)],
                [
                    'pasajero_id' => $passengers[$i]->id,
                    'vuelo_id' => $flights[$i]->id,
                    'clase' => Reserva::CLASES[$i % count(Reserva::CLASES)],
                    'estado' => Reserva::ESTADOS[$i % count(Reserva::ESTADOS)],
                    'asiento' => sprintf('%d%s', ($i % 10) + 1, chr(65 + ($i % 6))),
                    'precio' => 450 + ($i * 37.5),
                ]
            );
        }
    }

    private function calculateDistance(Aeropuerto $origin, Aeropuerto $destination): int
    {
        $earthRadius = 6371;
        $latFrom = deg2rad((float) $origin->latitud);
        $lonFrom = deg2rad((float) $origin->longitud);
        $latTo = deg2rad((float) $destination->latitud);
        $lonTo = deg2rad((float) $destination->longitud);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2)
            + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return (int) round($earthRadius * $angle);
    }
}
