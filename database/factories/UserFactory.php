<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => "Derek",
            'email' => "dmurillo@inscamidemar.cat",
            'password' => bcrypt('123'),
            'birth_date' => now(),
            'phone' => 622391128,
            'shipping_address' => "Carrer Saturn n9",
            'billing_address' => "Carrer Saturn n9",
            'document_id' => '21705160Z',
            'remember_token' => Str::random(10),
        ];
    }
}
