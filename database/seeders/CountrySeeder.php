<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void {
        
        $countries = [
            ["serial" => "CN-001",
            "name" => "Sudan",
            "nameAr" => "السودان",
            "currency" => "SDN",
            "toSDG" => "1"],

            ["serial" => "CN-002",
            "name" => "United Kingdom",
            "nameAr" => "بريطانيا",
            "currency" => "GBP",
            "toSDG" => "1"],

            ["serial" => "CN-003",
            "name" => "Ireland",
            "nameAr" => "إيرلندا",
            "currency" => "EUR",
            "toSDG" => "1"]
        ];

        foreach ($countries as $country) {  

            DB::table('countries')->insert([
                'serial' => $country['serial'],
                'name' => $country['name'],
                'nameAr' => $country['nameAr'],
                'currency' => $country['currency'],
                'toSDG' => $country['toSDG'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } // end loop

    } // end function
    
} // end seeder
