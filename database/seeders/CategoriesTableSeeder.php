<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Work', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ideas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tasks', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'General', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        DB::table('categories')->insert($categories);
    }
}