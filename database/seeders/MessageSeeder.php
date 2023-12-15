<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder {
    public function run(): void {
        
        $messages = [
            ["isFor" => "phone",
            "type" => "otp",
            "content" => "",
            "contentAr" => ""],


            // ------------------
            // ------------------


            ["isFor" => "delivery",
            "type" => "processing",
            "content" => "",
            "contentAr" => ""],

            ["isFor" => "delivery",
            "type" => "canceled",
            "content" => "",
            "contentAr" => ""],


            ["isFor" => "delivery",
            "type" => "completed",
            "content" => "",
            "contentAr" => ""],


            // ------------------
            // ------------------
            

            ["isFor" => "pickup",
            "type" => "processing",
            "content" => "",
            "contentAr" => ""],

            ["isFor" => "pickup",
            "type" => "canceled",
            "content" => "",
            "contentAr" => ""],

            ["isFor" => "pickup",
            "type" => "completed",
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
