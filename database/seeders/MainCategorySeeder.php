<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MainCategorySeeder extends Seeder
{
    
    public function run(): void {
        
        for ($i=0; $i < 100; $i++) { 
            DB::table('main_categories')->insert([
                'serial' => 'EM-' . $i+1,
                'name' => 'Main Category' . $i+1,
                'nameAr' => '-',
                'index' => $i+10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }


    } // end function
    
} // end seeder
