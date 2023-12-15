<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GeneralBlockSeeder extends Seeder
{

    public function run(): void {
        
        DB::table('general_blocks')->insert([
            'stopPickup' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    } // end function

} // end seeder
