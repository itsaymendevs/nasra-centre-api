<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use stdClass;
use App\Traits\AppTrait;



class UserExport implements FromCollection, WithHeadings
{




    // :: use trait
    use AppTrait;

    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function headings() : array
    {
        return [
            "ID",
            "Name",
            "Phone Number",
            "E-mail Address",
            "Disabled Account",

            "Country",
            "State",
            "Region",
            "Address Description",


            "no. of Orders",
            "no. of Completed Orders",
            "no. of Canceled Orders",



        ];
    } // end headings









    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function collection()
    {


        // 1: get User
        $combineUsers = array();
        $users = User::where('countryId', 1)->get();


        foreach ($users as $user) {

            $content = new stdClass();

            // 1: general
            $content->id = $this->createSerial('UID', intval($user->id) - 1);
            $content->name = $user->firstName . ' ' . $user->lastName;
            $content->phone = '+[' . strval($user->phone) . ']';
            $content->email = $user->email;
            $content->isDisabled = boolval($user->isActive) === true ? 'False' : 'True';



            // 2: local Address
            $content->country = $user->country->name;

            $content->state = $user->country->code == 'SD' ? $user->state->name : '';
            $content->region = $user->country->code == 'SD' ? $user->deliveryArea->name : '';
            $content->address = $user->country->code == 'SD' ? $user->address : '';



            // 3: orders
            $content->ordersCount = $user->orders->count() != 0 ? $user->orders->count() : '0';
            $content->completedOrdersCount = $user->completedOrders->count() != 0 ? $user->completedOrders->count() : '0';
            $content->canceledOrdersCount = $user->canceledOrders->count() != 0 ? $user->canceledOrders->count() : '0';



            array_push($combineUsers, $content);


        } // end loop



        return collect($combineUsers);


    } // end function



} // end export
