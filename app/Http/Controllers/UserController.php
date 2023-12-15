<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {

    public function login() {

        // 1: get user
        $user = User::all()->first();


        // 2: create token

        $tokenResult = $user->createToken('123123');
        $token = $tokenResult->plainTextToken;

        return response()->json(['token' => $token, 'tokenType' => 'Bearer'], 200);

    } // end function


    // ----------------------------------------------------------




} // end controller
