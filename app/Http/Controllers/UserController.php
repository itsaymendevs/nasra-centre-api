<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DeliveryArea;
use App\Models\State;
use App\Models\User;
use App\Models\UserReceiver;
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






    // ----------------------------------------------------------




    public function toggleActive(Request $request, $id) {


        // 1: get user
        $user = User::find($id);
        
        $user->isActive = !boolval($user->isActive);
        $user->save();


        return response()->json(['status' => true, 'message' => 'Status2 has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function singleUser(Request $request, $id) {


        // 1: get users / dependencies
        $user = User::with(['country', 'state', 'deliveryArea', 'favorites.product', 'receivers'])->where('id', $id)->first();
        $countries = Country::all();
        

        // 1.2: combine
        $combine = new stdClass();

        $combine->user = $user;
        $combine->countries = $countries;



        return response()->json($combine, 200);

    } // end function







    // ----------------------------------------------------------




    public function singleReceiver(Request $request, $id, $receiverId) {


        // 1: get receiver / dependencies
        $receiver = UserReceiver::with(['state.country', 'deliveryArea', 'user.country'])->where('id', $receiverId)->first();
      
        

        return response()->json($receiver, 200);

    } // end function

    


} // end controller
