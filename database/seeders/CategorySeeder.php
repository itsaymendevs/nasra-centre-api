<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder {
    public function run(): void {
        
        DB::table('categories')->insert([
            'image' => null,
            'imageAr' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


    } // end function
    
} // end seeder
