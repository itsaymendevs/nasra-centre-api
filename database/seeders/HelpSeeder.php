<?php

namespace Database\Seeders;

use App\Models\MediaInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HelpSeeder extends Seeder {
   
    public function run(): void {

        DB::table('media_infos')->insert([
            'websiteURL' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        DB::table('address_infos')->insert([
            'latitude' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    } // end function
} // end seeder
