<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function ($table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn(['category_id', 'subcategory_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::table('cars', function ($table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
