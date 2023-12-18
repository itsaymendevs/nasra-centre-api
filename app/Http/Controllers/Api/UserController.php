<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLead;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use stdClass;
use Throwable;

class UserController extends Controller {
    



    public function login (Request $request) {


        // 1: get use -> phone
        $response = new stdClass();
        $response->errors = array();

        $user = User::where('phone', $request->phone)->first();






        // 2: Check For Errors

        // 2.1: Phone
        if (empty($request->phone)) {

            $response->errors[0] = 10;
            return response()->json($response);

        } // end if



        // 2.2: Password
        if (empty($request->password)) {

            $response->errors[0] = 10;
            return response()->json($response);

        } // end if


        

        // 2.3: Credit Incorrect
        if (!$user || !Hash::check($request->password, $user->password)) {

            $response->errors[0] = 10;
            return response()->json($response);

        } // end if



        // 2.4: Account Not-active
        if (boolval($user->isActive) === false) {

            $response->errors[0] = 15;
            return response()->json($response);

        } // end if







        

        // 3: Delete Old-Tokens
        try {

            $user->tokens()->delete();

        } catch (Throwable $event) {}
        


        // 3.1: Create Token
        $token = $user->createToken('AppToken')->plainTextToken;


        // info to send with user
        $content = new stdClass();
        $content->id = $user->id;

        $content->firstName = $user->firstName;
        $content->lastName = $user->lastName;
        $content->email = $user->email;
        $content->phone = intval($user->phone);
        
        $content->stateId = $user->stateId;
        $content->regionId = $user->deliveryAreaId;
        
        $content->address = $user->address;
        $content->deliveryPrice = $user->deliveryArea->price;
        $content->deliveryBlocked = !boolval($user->deliveryArea->isActive);

        $content->deliveryTime = $user->deliveryArea->deliveryTime->content;
        $content->deliveryTimeAr = $user->deliveryArea->deliveryTime->contentAr;
        

        $response = new stdClass();
        $response->user = $content;
        $response->token = $token;
   

        // return response in json
        return response()->json($response);



    } // end function


    



    // -----------------------------------------------------------------









    public function register (Request $request) {


        // :: root
        $response = new stdClass();
        $expireTime = 1;
        $expireDelete = false;
        $request->isArSMS == "true" ? $lang = "arabic" : $lang = "english";


        // ::root - convert array to objects
        $request = (object) $request->all();
        $request->newUserData = (object) $request->newUserData;







        // 1.1: Phone / checkValid
        $userPhone = strval($request->newUserData->phone);
        $isPhoneValid = $this->checkPhone($userPhone);


        // =============================
        // =============================


   

        // 2: check if duplicated (In Use)
        $isDuplicated = User::where('phone', $userPhone)->count();
        



        // 2.1: check if duplicated (In UserTemp) / (if exist check minutes difference)
        $isDuplicatedTemp = UserLead::where('phone', $userPhone)->count();


        if ($isDuplicatedTemp >= 1) {

            // 2.1.1: check if expired or not
            $userLead = UserLead::where('phone', $userPhone)->first();



            // 2.1.2: prepare timing difference
            $todayDate = new DateTime();

            $previousDate = $userLead->created_at;
            $timeDifference = $todayDate->diff($previousDate);

            // get the minutes
            $minutes = $timeDifference->days * 24 * 60;
            $minutes += $timeDifference->h * 60;
            $minutes += $timeDifference->i;

            if ($minutes > $expireTime) {

                // :Flag to delete expired UserLead / :Reset Flag to Continue with register
                $expireDelete = true;
                $isDuplicatedTemp = 0;

            } // otp expired

        } //there's a similar number in db temp

        





        // 3: Register Filters
        $errorKeys = $this->registerFilters($request, $isDuplicated, $isDuplicatedTemp, $isPhoneValid);



        // ::prepare response with Errors
        if (count($errorKeys->errors) > 0) {

            return response()->json($errorKeys);

        } // end if
        





        // =============================
        // =============================



        // 4: Send OTP
        $otpResponse = $this->sendOTP($userPhone, $lang, $expireDelete);





        
        // 4.2: handle otp - errors / success response
        if (!empty($otpResponse->errors)) {

            $response->errors = $otpResponse->errors;
                
        } else {


            $response = new stdClass();
            $response->verificationCode = intval($otpResponse->otp);

        } // end if



        // return response in json
        return response()->json($response);
        

    } //end of register function











    



    // -----------------------------------------------------------------








    public function confirmRegister(Request $request) {


        // :: root
        $response = new stdClass();
        $response->errors = array();

        $expireTime = 1;
        $isOtpConfirmed = false;

  
        // ::root - convert array to objects
        $request = (object) $request->all();
        $request->newUserData = (object) $request->newUserData;






        // 1.1: Phone / checkValid
        $userPhone = strval($request->newUserData->phone);


        // 1.2: otp
        $userOtp = $request->enteredVerificationCode;





        // 1.3: check-user
        $userLead = UserLead::where('phone', $userPhone)->first();


        // 1.4: check otp-expired
        if ($userLead) {


            $todayDate = new DateTime();
            $previousDate = $userLead->created_at;
            $timeDiff = $todayDate->diff($previousDate);


            // :: get the minutes
            $minutes = $timeDiff->days * 24 * 60;
            $minutes += $timeDiff->h * 60;
            $minutes += $timeDiff->i;

            // 1.4.1: otp expired - delete it
            if ($minutes > $expireTime) {

                UserLead::where('phone', $userPhone)->delete();
                
                $response->errors[0] = 12; 
                return response()->json($response);

            } // end if



        // 1.5: not found
        } else {

            $response->errors[0] = 12;
            return response()->json($response);

        } // end if


        



        // ==========================
        // ==========================





        // 2: otp mismatch
        if ($userOtp == $userLead->otp) {

            $isOtpConfirmed = true;

        } else {

            $response->errors[0] = 13;
            return response()->json($response);

        } // end if




        

        // 3: otp confirmed - Return UserModal
        if ($isOtpConfirmed === true) {

            // 3.1: create user
            $user = new User();

            $user->firstName = $request->newUserData->firstName;
            $user->lastName = $request->newUserData->lastName;

            $user->email = $request->newUserData->email;
            $user->phone = $request->newUserData->phone;
            $user->address = $request->newUserData->address;

            $user->password = Hash::make($request->newUserData->password);

            $user->countryId = 1;
            $user->stateId = $request->newUserData->stateId;
            $user->deliveryAreaId = $request->newUserData->regionId;

            $user->save();





            // 3.2: create token / User
            $token = $user->createToken('AppToken')->plainTextToken;


            $content = new stdClass();
            $content->id = $user->id;

            $content->firstName = $user->firstName;
            $content->lastName = $user->lastName;
            $content->email = $user->email;

            $content->phone = intval($user->phone);
            $content->stateId = $user->stateId;
            $content->regionId = $user->deliveryAreaId;
            $content->address = $user->address;

            $content->deliveryPrice = doubleval($user->deliveryArea->price);
            $content->deliveryBlocked = !boolval($user->deliveryArea->isActive);

            $content->deliveryTime = $user->deliveryArea->deliveryTime->content;
            $content->deliveryTimeAr = $user->deliveryArea->deliveryTime->contentAr;



            // 3.3: join response
            $response = new stdClass();
            $response->user = $content;
            $response->token = $token;

          


            // 3.4: Delete UserLeads
            UserLead::where('phone', $userPhone)->delete();




            // ::prepare response
            return response()->json($response);


        } //end of it



    } //end of function











    // -----------------------------------------------------------------







    public function logout (Request $request) {

        $request->user()->currentAccessToken()->delete();


        // ::prepare response
        $response = new stdClass();
        $response->message = 'Logged Out!';

        return response()->json($response);

    } // end function




    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    // -----------------------------------------------------------------










    protected function registerFilters($request, $isDuplicated, $isDuplicatedTemp, $isPhoneValid) {

        $counter = 0;
        $errorKeys = new stdClass();
        $errorKeys->errors = array();


        // 1: firstName
        if (empty($request->newUserData->firstName)) {

            $errorKeys->errors[$counter] = 1; $counter++;

        } // end if



        // 2: lastName
        if (empty($request->newUserData->lastName)) {

            $errorKeys->errors[$counter] = 2; $counter++;

        } // end if





        // 3: Phone / Phone match
        if (empty($request->newUserData->phone)) {

            $errorKeys->errors[$counter] = 3; $counter++;


        } elseif ($isPhoneValid === false) {

            $errorKeys->errors[$counter] = 3; $counter++;


        } elseif ($isDuplicated == 1) {

            $errorKeys->errors[$counter] = 4; $counter++;

        } elseif ($isDuplicatedTemp == 1) {

            $errorKeys->errors[$counter] = 14; $counter++;

        } // end if



        // 4: Email
        if (empty($request->newUserData->email)) {

            $errorKeys->errors[$counter] = 5; $counter++;

        } // end if


        // 5: Password + regionId + stateId
        if (empty($request->newUserData->password) || empty($request->newUserData->regionId) || empty($request->newUserData->stateId)) {

            $errorKeys->errors[$counter] = 17; $counter++;

        } // end if


        // 6: address description invalid
        if (empty($request->newUserData->address)) {

            $errorKeys->errors[$counter] = 9; $counter++;

        } // end if




        // :: Return List
        return $errorKeys;


    } // end function









    // -----------------------------------------------------------------






    protected function checkPhone($userPhone) {

        // ::root
        $userPhone = strval($userPhone);


        
        // 1: check length => (must be 12 - eg : 249 99 959 0002)
        if (strlen($userPhone) != 12) return false; else return true;


    } //end of check phone














    // -----------------------------------------------------------------









    public function sendOTP($userPhone, $lang, $expireDelete) {


        // :: root
        $otpResponse = new stdClass();
        $otpCode = mt_rand(1000, 9999);



        // 1: check if otp unique
        while(true) {

            $otpDuplicated = UserLead::where('otp', $otpCode)->count();

            // 1.2: re-generate
            if ($otpDuplicated  > 0) $otpCode = mt_rand(1000, 9999); else break;

        } // end while




        // 2: Otp Provider EN / AR
        // if ($lang == "english") {

        //     $response = Http::get('https://www.airtel.sd/bulksms/webacc.aspx', [
        //         'user' => 'nasra',
        //         'pwd' => '540125',
        //         'Sender' => 'Nasra', // 11 char max
        //         'Nums' => $userPhone, // 249 99 959 0002 (like this) separated by (;) no space
        //         'smstext' => 'Your Verification Code : ' . $otpCode, // 70 char per message - 160 (latin)
        //     ]);

        // } else {

        //     $response = Http::get('https://www.airtel.sd/bulksms/webacc.aspx', [
        //         'user' => 'nasra',
        //         'pwd' => '540125',
        //         'Sender' => 'Nasra', // 11 char max
        //         'Nums' => $userPhone, // 249 99 959 0002 (like this) separated by (;) no space
        //         'smstext' => 'رقم التأكيد الخاص بك : ' . $otpCode, //70 char per message - 160 (latin)
        //     ]);

        // } // end if
        








        // 3: Delete User from Lead
        if ($expireDelete === true) {

            UserLead::where('phone', $userPhone)->delete();

        } // end if





        // 1: Add Lead to DB
        $userLead = new UserLead();

        $userLead->phone = $userPhone;
        $userLead->otp = $otpCode;

        $userLead->save();

        

        // ::prepare response
        $otpResponse->otp = $otpCode;

        return $otpResponse;


    } // end function





} // end controller
