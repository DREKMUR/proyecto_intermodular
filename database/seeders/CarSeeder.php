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
                'stock' => 5,
                'brand_id' => 1,
                'model' => 'Civic',
                'year' => 2020,
                'specs' => [
                    'engine' => '2.0L',
                    'hp' => 158,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Honda Civic',
                'category' => CarTypes::Compact,
                'state' => CarStates::Available,
                'price' => 21500,
                'imageRoute' => 'images/honda_civic.png',
                'discount' => 0,
            ],
            [
                'stock' => 3,
                'brand_id' => 2,
                'model' => 'Lancer Evo X',
                'year' => 2015,
                'specs' => [
                    'engine' => '2.0L Turbo',
                    'hp' => 291,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Mitsubishi Lancer Evolution X',
                'category' => CarTypes::Sport,
                'state' => CarStates::Reserved,
                'price' => 32000,
                'imageRoute' => 'images/lancer.png',
                'discount' => 1500,
            ],
            [
                'stock' => 2,
                'brand_id' => 3,
                'model' => '911 Carrera',
                'year' => 2022,
                'specs' => [
                    'engine' => '3.0L Twin-Turbo',
                    'hp' => 379,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Porsche 911 Carrera',
                'category' => CarTypes::Sport,
                'state' => CarStates::Available,
                'price' => 98000,
                'imageRoute' => 'images/porsche.png',
                'discount' => 5000,
            ],
            [
                'stock' => 4,
                'brand_id' => 4,
                'model' => 'Impreza WRX STI',
                'year' => 2018,
                'specs' => [
                    'engine' => '2.5L Turbo',
                    'hp' => 310,
                    'fuel' => 'Gasolina'
                ],
                'name' => 'Subaru WRX STI',
                'category' => CarTypes::Sport,
                'state' => CarStates::Sold,
                'price' => 36000,
                'imageRoute' => 'images/subaru.png',
                'discount' => 1000,
            ],
            [
                'stock' => 6,
                'brand_id' => 5,
                'model' => 'RAV4 Hybrid',
                'year' => 2021,
                'specs' => [
                    'engine' => '2.5L Hybrid',
                    'hp' => 219,
                    'fuel' => 'Híbrido'
                ],
                'name' => 'Toyota RAV4 Hybrid',
                'category' => CarTypes::Suv,
                'state' => CarStates::Available,
                'price' => 34500,
                'imageRoute' => 'images/coche_rojo.png',
                'discount' => 500,
            ],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}
