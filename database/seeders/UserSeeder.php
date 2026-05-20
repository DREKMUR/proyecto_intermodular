<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@demufe.com',
            'password'          => bcrypt('123'),
            'is_admin'          => true,
            'birth_date'        => '1990-01-01',
            'phone'             => '600000001',
            'shipping_address'  => 'Carrer del Admin, 1',
            'billing_address'   => 'Carrer del Admin, 1',
            'document_id'       => '00000001A',
        ]);

        $users = [
            ['name' => 'Derek Murillo', 'email' => 'dmurillo@inscamidemar.cat', 'phone' => '622391128', 'document_id' => '21705160Z', 'shipping_address' => 'Carrer Saturn n9', 'billing_address' => 'Carrer Saturn n9'],
            ['name' => 'Laura García',  'email' => 'lgarcia@example.com',      'phone' => '600000002', 'document_id' => '00000002B', 'shipping_address' => 'Carrer Major 5',   'billing_address' => 'Carrer Major 5'],
            ['name' => 'Marc López',    'email' => 'mlopez@example.com',       'phone' => '600000003', 'document_id' => '00000003C', 'shipping_address' => 'Av. del Mar 12',   'billing_address' => 'Av. del Mar 12'],
            ['name' => 'Anna Costa',    'email' => 'acosta@example.com',       'phone' => '600000004', 'document_id' => '00000004D', 'shipping_address' => 'Plaça Gran 8',     'billing_address' => 'Plaça Gran 8'],
            ['name' => 'Pau Roca',      'email' => 'proca@example.com',        'phone' => '600000005', 'document_id' => '00000005E', 'shipping_address' => 'Carrer Nou 22',    'billing_address' => 'Carrer Nou 22'],
        ];

        foreach ($users as $user) {
            User::create(array_merge($user, [
                'password'    => bcrypt('123'),
                'is_admin'    => false,
                'birth_date'  => now()->subYears(rand(20, 50))->format('Y-m-d'),
            ]));
        }
    }
}
