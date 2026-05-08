<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'country',
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'logo' => 'string',
        'country' => 'string',
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
