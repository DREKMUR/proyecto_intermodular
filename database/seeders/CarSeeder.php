<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Enums\CarStates;
use App\Enums\CarTypes;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            [
                'brand_id' => 7,
                'model' => 'Skyline KGC10 "Bosozoku"',
                'year' => 1972,
                'specs' => [
                    'engine' => '2.0L L20 I6',
                    'hp' => 160,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Nissan Skyline Bosozoku Edition',
                'category' => CarTypes::ClassicSport,
                'price' => 45000,
                'imageRoute' => '/storage/images/bosozoku.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 1,
                'model' => 'DIY Cardboard',
                'year' => 2024,
                'specs' => [
                    'engine' => 'Piernas humanas',
                    'hp' => 1,
                    'fuel' => 'Agua'
                ],
                'name' => 'Carton Car Special',
                'category' => CarTypes::Compact,
                'price' => 50,
                'imageRoute' => '/storage/images/carton.avif',
                'discount' => 10,
            ],
            [
                'brand_id' => 8,
                'model' => 'Corvette C5',
                'year' => 2002,
                'specs' => [
                    'engine' => '5.7L LS1 V8',
                    'hp' => 350,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Chevrolet Corvette C5',
                'category' => CarTypes::Sport,
                'price' => 28000,
                'imageRoute' => '/storage/images/corvette-c5.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 2,
                'model' => 'Civic Coupe',
                'year' => 1998,
                'specs' => [
                    'engine' => '1.6L B16',
                    'hp' => 160,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Honda Civic Si',
                'category' => CarTypes::Coupe,
                'price' => 12000,
                'imageRoute' => '/storage/images/honda_civic.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 2,
                'model' => 'Civic Hatchback Type R',
                'year' => 2000,
                'specs' => [
                    'engine' => '1.6L B16B',
                    'hp' => 185,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Honda Civic Type R EK9',
                'category' => CarTypes::Compact,
                'price' => 25000,
                'imageRoute' => '/storage/images/honda_civic_hatchback.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 3,
                'model' => 'Lancer Evolution VI',
                'year' => 1999,
                'specs' => [
                    'engine' => '2.0L Turbo 4G63',
                    'hp' => 276,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Mitsubishi Lancer Evo VI',
                'category' => CarTypes::Sport,
                'price' => 42000,
                'imageRoute' => '/storage/images/lancer.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 2,
                'model' => 'NSX',
                'year' => 1990,
                'specs' => [
                    'engine' => '3.0L V6 VTEC',
                    'hp' => 270,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Acura/Honda NSX Classic',
                'category' => CarTypes::ClassicSport,
                'price' => 85000,
                'imageRoute' => '/storage/images/nissan-nsx.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 4,
                'model' => '911 GT3',
                'year' => 2018,
                'specs' => [
                    'engine' => '4.0L Flat-6',
                    'hp' => 500,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Porsche 911 GT3 RS',
                'category' => CarTypes::Sport,
                'price' => 185000,
                'imageRoute' => '/storage/images/porsche.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 9,
                'model' => '5 Turbo',
                'year' => 1980,
                'specs' => [
                    'engine' => '1.4L Turbo I4',
                    'hp' => 158,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Renault 5 Turbo',
                'category' => CarTypes::ClassicSport,
                'price' => 95000,
                'imageRoute' => '/storage/images/renault.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 6,
                'model' => 'Supra A80 Orange',
                'year' => 1994,
                'specs' => [
                    'engine' => '3.0L 2JZ-GTE',
                    'hp' => 320,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Toyota Supra Fast Edition',
                'category' => CarTypes::Sport,
                'price' => 75000,
                'imageRoute' => '/storage/images/subaru.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 6,
                'model' => 'Supra A80',
                'year' => 1998,
                'specs' => [
                    'engine' => '3.0L 2JZ',
                    'hp' => 280,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Toyota Supra Silver Carbon',
                'category' => CarTypes::Sport,
                'price' => 68000,
                'imageRoute' => '/storage/images/supra.avif',
                'discount' => 0,
            ],
            [
                'brand_id' => 10,
                'model' => 'Fleetwood "Firemaker"',
                'year' => 1975,
                'specs' => [
                    'engine' => '8.2L V8',
                    'hp' => 190,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Cadillac Fleetwood Lowrider',
                'category' => CarTypes::Lowrider,
                'price' => 35000,
                'imageRoute' => '/storage/images/cadillac-firemaker.avif',
                'discount' => 0,
            ],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}
