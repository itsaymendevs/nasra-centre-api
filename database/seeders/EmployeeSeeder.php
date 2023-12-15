<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void {
        
        DB::table('employees')->insert([
            'serial' => 'EM-001',
            'name' => 'Admin',
            'nameAr' => 'الأدمن',
            'password' => Hash::make('nasracentre'),
            'permission' => 'High',
            'isActive' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

    } // end function
    
} // end seeder
