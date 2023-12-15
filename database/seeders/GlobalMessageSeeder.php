<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GlobalMessageSeeder extends Seeder {


    public function run(): void {

        $messages = [
            ["isFor" => "phone",
            "type" => "otp",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],


            // ------------------
            // ------------------


            ["isFor" => "delivery",
            "type" => "processing",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],



            ["isFor" => "delivery",
            "type" => "canceled",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],


            ["isFor" => "delivery",
            "type" => "canceled",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],




            ["isFor" => "delivery",
            "type" => "completed",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],


            ["isFor" => "delivery",
            "type" => "completed",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],




            ["isFor" => "delivery",
            "type" => "reception",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],



            // ------------------
            // ------------------
            

            ["isFor" => "pickup",
            "type" => "processing",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],



            ["isFor" => "pickup",
            "type" => "canceled",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],

            ["isFor" => "pickup",
            "type" => "canceled",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],





            ["isFor" => "pickup",
            "type" => "completed",
            "content" => "",
            "contentAr" => "",
            'target' => 'customer'],

            ["isFor" => "pickup",
            "type" => "completed",
            "content" => "",
            "contentAr" => "",
            'target' => 'receiver'],




            ["isFor" => "pickup",
            "type" => "reception",
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
