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
            "code" => "SD",
            "currency" => "SDN",
            "toSDG" => "1"],

            ["serial" => "CN-002",
            "name" => "United Kingdom",
            "nameAr" => "بريطانيا",
            "code" => "UK",
            "currency" => "GBP",
            "toSDG" => "100"],

            ["serial" => "CN-003",
            "name" => "Ireland",
            "nameAr" => "إيرلندا",
            "code" => "IRL",
            "currency" => "EUR",
            "toSDG" => "150"],

            ["serial" => "CN-004",
            "name" => "Egypt",
            "nameAr" => "مصر",
            "code" => "EG",
            "currency" => "EGP",
            "toSDG" => "20"]

        ];

        foreach ($countries as $country) {  

            DB::table('countries')->insert([
                'serial' => $country['serial'],
                'name' => $country['name'],
                'nameAr' => $country['nameAr'],
                'code' => $country['code'],
                'currency' => $country['currency'],
                'toSDG' => $country['toSDG'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } // end loop

    } // end function
    
} // end seeder
