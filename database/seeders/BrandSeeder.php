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
            ['id' => 1, 'name' => 'Unknown', 'logo' => 'images/brand_logos/Unknown.avif', 'country' => 'Unknown'],
            ['id' => 2, 'name' => 'Honda', 'logo' => 'images/brand_logos/Honda.avif', 'country' => 'Japan'],
            ['id' => 3, 'name' => 'Mitsubishi', 'logo' => 'images/brand_logos/Mitsubishi.avif', 'country' => 'Japan'],
            ['id' => 4, 'name' => 'Porsche', 'logo' => 'images/brand_logos/Porsche.avif', 'country' => 'Germany'],
            ['id' => 5, 'name' => 'Subaru', 'logo' => 'images/brand_logos/Subaru.avif', 'country' => 'Japan'],
            ['id' => 6, 'name' => 'Toyota', 'logo' => 'images/brand_logos/Toyota.avif', 'country' => 'Japan'],
            ['id' => 7, 'name' => 'Nissan', 'logo' => 'images/brand_logos/Nissan.avif', 'country' => 'Japan'],
            ['id' => 8, 'name' => 'Chevrolet', 'logo' => 'images/brand_logos/Chevrolet.avif', 'country' => 'USA'],
            ['id' => 9, 'name' => 'Renault', 'logo' => 'images/brand_logos/Renault.avif', 'country' => 'France'],
            ['id' => 10, 'name' => 'Cadillac', 'logo' => 'images/brand_logos/Cadillac.avif', 'country' => 'USA'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['id' => $brand['id']], $brand);
        }

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'logo' => $brand['logo'],
                'country' => $brand['country'],
            ]);
        }
    }
}
