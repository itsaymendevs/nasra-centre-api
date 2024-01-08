<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use stdClass;


class UserExport implements FromCollection, WithHeadings
{



    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function headings() : array
    {
        return [
            "ID",
            "First Name",
            "Last Name",
            "Email Address",
            "Phone",
            "Second Phone",
            "Country"
        ];
    } // end headings









    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function collection()
    {


        // 1: get User
        $combineUsers = array();
        $users = User::all();


        foreach ($users as $user) {

            $content = new stdClass();

            $content->id = $user->id;
            $content->firstName = $user->firstName;
            $content->lastName = $user->lastName;
            $content->email = $user->email;
            $content->phone = $user->phone;
            $content->phoneAlt = $user->phoneAlt;
            $content->country = $user->country->name;

            array_push($combineUsers, $content);

        } // end loop



        return collect($combineUsers);


    } // end function
} // end export
