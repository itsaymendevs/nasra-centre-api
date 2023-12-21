<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DeliveryArea;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class UserController extends Controller {



    public function login() {

        // 1: get user
        $user = User::all()->first();


        // 2: create token
        $tokenResult = $user->createToken('DashboardToken');
        $token = $tokenResult->plainTextToken;

        return response()->json(['token' => $token, 'tokenType' => 'Bearer'], 200);

    } // end function





    // ----------------------------------------------------------



    public function index (Request $request) {


        // 1: get users / filters
        $users = User::all();

        $countries = Country::all();
        $states = State::all();
        $deliveryAreas = DeliveryArea::all();
        

        // 1.2: combine
        $combine = new stdClass();

        $combine->users = $users;
        $combine->countries = $countries;
        $combine->states = $states;
        $combine->deliveryAreas = $deliveryAreas;



        return response()->json($combine, 200);


    } // end function




} // end controller
