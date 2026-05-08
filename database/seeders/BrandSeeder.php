<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Honda',
                'logo' => 'images/honda_civic.png',
                'country' => 'Japan',
            ],
            [
                'name' => 'Mitsubishi',
                'logo' => 'images/lancer.png',
                'country' => 'Japan',
            ],
            [
                'name' => 'Porsche',
                'logo' => 'images/porsche.png',
                'country' => 'Germany',
            ],
            [
                'name' => 'Subaru',
                'logo' => 'images/subaru.png',
                'country' => 'Japan',
            ],
            [
                'name' => 'Toyota',
                'logo' => 'images/coche_rojo.png',
                'country' => 'Japan',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'logo' => $brand['logo'],
                'country' => $brand['country'],
            ]);
        }
    }
}
