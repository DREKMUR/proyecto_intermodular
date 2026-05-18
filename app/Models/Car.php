<?php

namespace App\Models;

use App\Enums\CarStates;
use App\Enums\CarTypes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'brand_id',
        'model',
        'year',
        'specs',
        'name',
        'category',
        'state',
        'price',
        'imageRoute',
        'discount',
    ];

    protected $casts = [
        'brand_id' => 'integer',
        'model' => 'string',
        'year' => 'integer',
        'specs' => 'array',
        'name' => 'string',
        'category' => CarTypes::class,
        'state' => CarStates::class,
        'price' => 'decimal:2',
        'imageRoute' => 'string',
        'discount' => 'decimal:2',
        'slug' => 'string',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (empty($this->attributes['slug']) && !empty($attributes['name']) && !empty($attributes['year'])) {
            $this->attributes['slug'] = Str::slug($attributes['name'] . '-' . $attributes['year']);
        }
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (!empty($this->attributes['year'])) {
            $this->attributes['slug'] = Str::slug($value . '-' . $this->attributes['year']);
        }
    }

    public function setYearAttribute($value)
    {
        $this->attributes['year'] = $value;
        if (!empty($this->attributes['name'])) {
            $this->attributes['slug'] = Str::slug($this->attributes['name'] . '-' . $value);
        }
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($car) {
            if (empty($car->slug) && !empty($car->name) && !empty($car->year)) {
                $base = Str::slug($car->name . '-' . $car->year);
                $slug = $base;
                $i = 1;
                while (self::where('slug', $slug)
                    ->when($car->exists, fn($q) => $q->where('id', '!=', $car->id))
                    ->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $car->slug = $slug;
            }
        });
    }
}
