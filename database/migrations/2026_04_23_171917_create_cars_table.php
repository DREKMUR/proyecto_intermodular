<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Enums\CarStates;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->string('model');
            $table->integer('year');
            $table->json('specs')->nullable();
            $table->string('name');
            $table->string('category');
            $table->string('state')->default(CarStates::Available);
            $table->decimal('price', 10, 2);
            $table->string('imageRoute');
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
