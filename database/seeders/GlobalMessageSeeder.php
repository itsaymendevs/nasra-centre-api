<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GlobalMessageSeeder extends Seeder {


    public function run(): void {

        $messages = [
            ["isFor" => "PHONE",
            "type" => "OTP",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],


            // ------------------
            // ------------------


            ["isFor" => "DELIVERY",
            "type" => "PROCESSING",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],



            ["isFor" => "DELIVERY",
            "type" => "CANCELED",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],


            ["isFor" => "DELIVERY",
            "type" => "CANCELED",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],




            ["isFor" => "DELIVERY",
            "type" => "COMPLETED",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],


            ["isFor" => "DELIVERY",
            "type" => "COMPLETED",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],




            ["isFor" => "DELIVERY",
            "type" => "RECEPTION",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],



            // ------------------
            // ------------------
            

            ["isFor" => "PICKUP",
            "type" => "PROCESSING",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],



            ["isFor" => "PICKUP",
            "type" => "CANCELED",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],

            ["isFor" => "PICKUP",
            "type" => "CANCELED",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],





            ["isFor" => "PICKUP",
            "type" => "COMPLETED",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],

            ["isFor" => "PICKUP",
            "type" => "COMPLETED",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],




            ["isFor" => "PICKUP",
            "type" => "RECEPTION",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],



        ];

        foreach ($messages as $message) {  

            DB::table('global_messages')->insert([
                'isFor' => $message['isFor'],
                'type' => $message['type'],
                'content' => $message['content'],
                'contentAr' => $message['contentAr'],
                'target' => $message['target'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } // end loop

    } // end function

} // end seeder
