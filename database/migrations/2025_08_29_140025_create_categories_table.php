<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ← ADD THIS LINE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');


    }


    // In database/seeders/DatabaseSeeder.php or create a new seeder
public function run()
{
    \App\Models\Category::create(['name' => 'Work']);
    \App\Models\Category::create(['name' => 'Personal']);
    \App\Models\Category::create(['name' => 'Ideas']);
    \App\Models\Category::create(['name' => 'Tasks']);
    
    // Then run: php artisan db:seed
}
};