<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    public function run(): void {
        
        $countries = Country::all();

        foreach ($countries as $country) {  

            DB::table('contacts')->insert([
                'countryId' => $country->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } // end loop

    } // end function
    
} // end seeder
