<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder {
    public function run(): void {
        
        $states = [
            ["serial" => "ST-001",
            "name" => "Khartoum",
            "nameAr" => "الخرطوم",
            "countryId" => 1],

            ["serial" => "ST-002",
            "name" => "Bahri",
            "nameAr" => "بحري",
            "countryId" => 1],

            ["serial" => "ST-003",
            "name" => "Omdurman",
            "nameAr" => "امدرمان",
            "countryId" => 1],

        ];

        foreach ($states as $state) {  

            DB::table('states')->insert([
                'serial' => $state['serial'],
                'name' => $state['name'],
                'nameAr' => $state['nameAr'],
                'countryId' => $state['countryId'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } // end loop

    } // end function
    
} // end seeder
