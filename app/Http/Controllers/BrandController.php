<?php

namespace App\Http\Controllers;

use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all()->map(function ($brand) {
            return [
                'name'   => $brand->name,
                'id' => $brand->id,
            ];
        });

        return $brands;
    }

}
