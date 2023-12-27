<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder {
    public function run(): void {
        
        $messages = [
            ["isFor" => "PHONE",
            "type" => "OTP",
            "content" => "",
            "contentAr" => ""],


            // ------------------
            // ------------------


            ["isFor" => "DELIVERY",
            "type" => "PROCESSING",
            "content" => "",
            "contentAr" => ""],

            ["isFor" => "DELIVERY",
            "type" => "CANCELED",
            "content" => "",
            "contentAr" => ""],


            ["isFor" => "DELIVERY",
            "type" => "COMPLETED",
            "content" => "",
            "contentAr" => ""],


            // ------------------
            // ------------------
            

            ["isFor" => "PICKUP",
            "type" => "PROCESSING",
            "content" => "",
            "contentAr" => ""],

            ["isFor" => "PICKUP",
            "type" => "CANCELED",
            "content" => "",
            "contentAr" => ""],

            ["isFor" => "PICKUP",
            "type" => "COMPLETED",
            "content" => "",
            "contentAr" => ""],

        ];

        foreach ($messages as $message) {  

            DB::table('messages')->insert([
                'isFor' => $message['isFor'],
                'type' => $message['type'],
                'content' => $message['content'],
                'contentAr' => $message['contentAr'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } // end loop

    } // end function
    
} // end seeder
