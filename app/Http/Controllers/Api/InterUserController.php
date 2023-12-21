<?php

namespace App\Http\Controllers\Api;


use App\Models\Country;
use App\Models\Product;
use App\Models\UserDevice;
use App\Models\UserFavorite;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use stdClass;
use Throwable;
use DateTime;


ini_set('max_execution_time', 180); // 180 (seconds) = 3 Minutes




class InterUserController extends Controller {
    



    public function login (Request $request) {


        // 1: get use -> phone
        $response = new stdClass();
        $response->errors = array();

        $user = User::where('phone', $request->phoneNumber)->first();






        // 2: Check For Errors

        // 2.1: Phone
        if (empty($request->phoneNumber)) {

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
        $content->emailAddress = $user->email;
        $content->phoneNumber = intval($user->phone);
        


        $content->userAddress = new stdClass();


        // 3.1.1: countryLettersCode
        $content->countryLettersCode = $user->country->code;

        // 3.1.2: uk address
        if ($user->country->code == 'UK') {

            $content->userAddress->addressFirstLine = $user->addressFirstLine;
            $content->userAddress->addressSecondLine = $user->addressSecondLine; // optional
            
            $content->userAddress->addressThirdLine = $user->addressThirdLine; // optional
            $content->userAddress->townCity = $user->townCity;
            $content->userAddress->postcode = $user->postcode;
            
        } // end if


        // 3.1.3: irl address
        if ($user->country->code == 'IRL') {

            $content->userAddress->addressFirstLine = $user->addressFirstLine;
            $content->userAddress->addressSecondLine = $user->addressSecondLine; // optional
            
            $content->userAddress->postTown = $user->postTown;
            $content->userAddress->county = $user->county; // optional
            $content->userAddress->eircode = $user->eircode; // optional
            
        } // end if





        
        // 3.2: receivers
        $content->receivers = array();

        foreach ($user->receivers as $receiver) {

            $item = new stdClass();

            $item->receiverId = $receiver->id;
            $item->receiverName = $receiver->name;
            $item->phoneNumber = $receiver->phone;
            $item->secondPhoneNumber = $receiver->phoneAlt;


            $item->receiverAddress = new stdClass();

            $item->receiverAddress->receiverStateId = $receiver->stateId;
            $item->receiverAddress->receiverRegionId = $receiver->deliveryAreaId;
            
            $item->receiverAddress->addressDescription = $receiver->address;

            $item->receiverAddress->deliveryEstimatedTime = $receiver->deliveryArea->deliveryTime->content;

            $item->receiverAddress->deliveryEstimatedTimeAr = $receiver->deliveryArea->deliveryTime->contentAr;

            $item->receiverAddress->regionDeliveryPrice = intval($receiver->deliveryArea->price);
            $item->receiverAddress->isDeliveryBlocked = !boolval($receiver->deliveryArea->isActive);




            array_push($content->receivers, $item);
            
        } // end loop







        // ::prepare response
        $response = new stdClass();
        $response->interUser = $content;
        $response->token = $token;





        // ==================================
        // ==================================









        // 4: fetch productsID List / Save Device (if New)
        $isDuplicated = UserDevice::where('userId', $user->id)
        ->where('serial', $request->deviceID)->count();


        // 4.1: fetch products
        $products = Product::whereIn('id', $request->productsID)->get();
        $contentArray = array();



        // 4.1.2: ProductsID is returned (in both options)
        foreach ($products as $product) {

            $content = new stdClass();
            $content->id = $product->id;
            $content->categoryId = $product->mainCategoryId;
            $content->subCategoryId = $product->subCategoryId;
            $content->typeId = $product->typeId;
            $content->companyId = $product->companyId;


            $content->name = $product->name;
            $content->nameAr = $product->nameAr;

            $content->mainPic = $product->image;
            $content->additionalPics = null;

            
            
            
            // ::determine productType (byName - fixedSize - dynamicSize)
            if ($product->weightOption == 'byName')
                $content->productType = 'NAMEFULL';

            else if ($product->weightOption == 'fixedSize')
                $content->productType = 'FIXED';

            else
                $content->productType = 'DYNAMIC';


            $content->measuringUnitId = $product->unitId;
            $content->minQuantityToOrder = $product->weight;

            $content->quantityAvailable = $product->quantity;
            $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
            $content->originalPrice = $product->sellPrice;
            $content->offerPrice = $product->offerPrice;

            $content->desc = $product->desc;
            $content->descAr = $product->descAr;


            array_push($contentArray, $content);

        } // end loop




        




        // 4.2: determine deviceID / FavoriteList actions
        if ($isDuplicated > 0) {



            // 4.2.1: remove previous favorites / update with ProductsIDs
            UserFavorite::where('userId', $user->id)->delete();

            foreach ($products as $product) {

                $userFavorite = new UserFavorite();
                $userFavorite->userId = $user->id;
                $userFavorite->productId = $product->id;

                $userFavorite->save();
                
            } // end loop

            


        } else {


            // ::root -> Save deviceID
            $userDevice = new UserDevice();
            $userDevice->userId = $user->id;
            $userDevice->serial = $request->deviceID;

            $userDevice->save();





            // 4.2.2: Favorites is returned / appended later on to returned
            $favoritesID = UserFavorite::where('userId', $user->id)->get(['productId'])->toArray();
            $favoriteProducts = Product::whereIn('id', $favoritesID)->get();


            


            // 4.2.3: ProductsIDs appended in favorites
            foreach ($products->whereNotIn('id', $favoritesID) as $product) {

                $userFavorite = new UserFavorite();
                $userFavorite->userId = $user->id;
                $userFavorite->productId = $product->id;

                $userFavorite->save();
                
            } // end loop







            // 4.2.4: favorites appended in returned
            foreach ($favoriteProducts as $product) {

                $content = new stdClass();
                $content->id = $product->id;
                $content->categoryId = $product->mainCategoryId;
                $content->subCategoryId = $product->subCategoryId;
                $content->typeId = $product->typeId;
                $content->companyId = $product->companyId;
    
    
                $content->name = $product->name;
                $content->nameAr = $product->nameAr;
    
                $content->mainPic = $product->image;
                $content->additionalPics = null;
    
                
                
                
                // ::determine productType (byName - fixedSize - dynamicSize)
                if ($product->weightOption == 'byName')
                    $content->productType = 'NAMEFULL';
    
                else if ($product->weightOption == 'fixedSize')
                    $content->productType = 'FIXED';
    
                else
                    $content->productType = 'DYNAMIC';
    
    
                $content->measuringUnitId = $product->unitId;
                $content->minQuantityToOrder = $product->weight;
    
                $content->quantityAvailable = $product->quantity;
                $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
                $content->originalPrice = $product->sellPrice;
                $content->offerPrice = $product->offerPrice;
    
                $content->desc = $product->desc;
                $content->descAr = $product->descAr;
    
    
                array_push($contentArray, $content);
    
            } // end loop



        } // end else







        // ::prepare response
        $response->favProducts = $contentArray;

        
        return response()->json($response);


    } // end function


    



    // -----------------------------------------------------------------









    public function register (Request $request) {


        // :: root
        $response = new stdClass();
        $expireTime = 1;
        $expireDelete = false;


        // ::root - convert array to objects
        $request = (object) $request->all();
        $request->newUserData = (object) $request->newUserData;
        $request->isArSMS === true ? $lang = "arabic" : $lang = "english";







        // 1.1: Phone / checkValid
        $userPhone = strval($request->newUserData->phoneNumber);
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










    public function registerResend(Request $request) {

        

        // :: root
        $response = new stdClass();
        $response->errors = array();
        $expireTime = 1;

 
        // ::root - convert array to objects
        $request = (object) $request->all();
        $request->isArSMS === true ? $lang = "arabic" : $lang = "english";






        // 1.1: Phone / checkValid
        $userPhone = strval($request->phoneNumber);





        // =============================
        // =============================




        // 2: check otp-expired
        $userLead = UserLead::where('phone', $userPhone)->first();


        if ($userLead->count() >= 1) {


            $todayDate = new DateTime();
            $previousDate = $userLead->created_at;
            $timeDiff = $todayDate->diff($previousDate);

            // ::get minutes
            $minutes = $timeDiff->days * 24 * 60;
            $minutes += $timeDiff->h * 60;
            $minutes += $timeDiff->i;


            // 2.1: if expired / remove from DB
            if ($minutes > $expireTime) {

                UserLead::where('phone', $userPhone)->delete();
                
                $response->errors[0] = 12;
                return response()->json($response);

            } // end if




        } else {

            $response->errors[0] = 12;
            return response()->json($response);

        } // end if









        // =============================
        // =============================



        // 3: resend Otp
        $otpResponse = $this->resendOTP($userPhone, $lang);


        
        // 4: handle Otp Response
        if (!empty($otpResponse->errors)) {

            $response->errors = $otpResponse->errors;
                
        } else {

            $response = new stdClass();
            $response->verificationCode = intval($otpResponse->otp);

        } // end if






        // ::prepare response
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
        $request->newUserData->userAddress = (object) $request->newUserData->userAddress;







        // 1.1: Phone / checkValid
        $userPhone = strval($request->newUserData->phoneNumber);


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

            $user->email = $request->newUserData->emailAddress;
            $user->phone = $request->newUserData->phoneNumber;
            $user->password = Hash::make($request->newUserData->password);



            // 3.1.1: countryLettersCode
            $country = Country::where('code', $request->newUserData->countryLettersCode)->first();
            $user->countryId = $country->id;


            // 3.1.2: uk address
            if ($country->code == 'UK') {

                $user->addressFirstLine = $request->newUserData->userAddress->phoneNumber;
                $user->addressSecondLine = $request->newUserData->userAddress->addressSecondLine ? $request->newUserData->userAddress->addressSecondLine : null; // optional
                
                $user->addressThirdLine = $request->newUserData->userAddress->addressThirdLine ? $request->newUserData->userAddress->addressThirdLine : null; // optional
                $user->townCity = $request->newUserData->userAddress->townCity;
                $user->postcode = $request->newUserData->userAddress->postcode;
                
            } // end if


            // 3.1.3: irl address
            if ($country->code == 'IRL') {

                $user->addressFirstLine = $request->newUserData->userAddress->addressFirstLine;
                $user->addressSecondLine = $request->newUserData->userAddress->addressSecondLine ? $request->newUserData->userAddress->addressSecondLine : null; // optional
                
                $user->postTown = $request->newUserData->userAddress->postTown;
                $user->county = $request->newUserData->userAddress->county ? $request->newUserData->userAddress->county : null; // optional
                $user->eircode = $request->newUserData->userAddress->eircode ? $request->newUserData->userAddress->eircode : null; // optional
                
            } // end if




            // 3.1.4: save information
            $user->save();







            // 3.2: create token / User
            $token = $user->createToken('AppToken')->plainTextToken;


            $content = new stdClass();
            $content->id = $user->id;

            $content->firstName = $user->firstName;
            $content->lastName = $user->lastName;
            $content->emailAddress = $user->email;
            $content->phoneNumber = intval($user->phone);
            


            $content->userAddress = new stdClass();


            // 3.1.1: countryLettersCode
            $content->countryLettersCode = $user->country->code;

            // 3.1.2: uk address
            if ($user->country->code == 'UK') {

                $content->userAddress->addressFirstLine = $user->addressFirstLine;
                $content->userAddress->addressSecondLine = $user->addressSecondLine; // optional
                
                $content->userAddress->addressThirdLine = $user->addressThirdLine; // optional
                $content->userAddress->townCity = $user->townCity;
                $content->userAddress->postcode = $user->postcode;
                
            } // end if


            // 3.1.3: irl address
            if ($user->country->code == 'IRL') {

                $content->userAddress->addressFirstLine = $user->addressFirstLine;
                $content->userAddress->addressSecondLine = $user->addressSecondLine; // optional
                
                $content->userAddress->postTown = $user->postTown;
                $content->userAddress->county = $user->county; // optional
                $content->userAddress->eircode = $user->eircode; // optional
                
            } // end if





            // 3.2: receivers
            $content->receivers = array();

            foreach ($user->receivers as $receiver) {

                $item = new stdClass();

                $item->receiverId = $receiver->id;
                $item->receiverName = $receiver->name;
                $item->phoneNumber = $receiver->phone;
                $item->secondPhoneNumber = $receiver->phoneAlt;


                $item->receiverAddress = new stdClass();

                $item->receiverAddress->receiverStateId = $receiver->stateId;
                $item->receiverAddress->receiverRegionId = $receiver->deliveryAreaId;
                
                $item->receiverAddress->addressDescription = $receiver->address;

                $item->receiverAddress->deliveryEstimatedTime = $receiver->deliveryArea->deliveryTime->content;

                $item->receiverAddress->deliveryEstimatedTimeAr = $receiver->deliveryArea->deliveryTime->contentAr;

                $item->receiverAddress->regionDeliveryPrice = intval($receiver->deliveryArea->price);
                $item->receiverAddress->isDeliveryBlocked = !boolval($receiver->deliveryArea->isActive);


                array_push($content->receivers, $item);
                
            } // end loop







            // 3.3: join response
            $response = new stdClass();
            $response->interUser = $content;
            $response->token = $token;

          


            // 3.4: Delete UserLeads
            UserLead::where('phone', $userPhone)->delete();













            // ==================================
            // ==================================



            // ::root -> Save deviceID
            $userDevice = new UserDevice();
            $userDevice->userId = $user->id;
            $userDevice->serial = $request->deviceID;

            $userDevice->save();









            // 4.1: ProductsIDs appended in favorites
            $products = Product::whereIn('id', $request->productsID)->get();

            foreach ($products as $product) {

                $userFavorite = new UserFavorite();
                $userFavorite->userId = $user->id;
                $userFavorite->productId = $product->id;

                $userFavorite->save();
                
            } // end loop







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
        if (empty($request->newUserData->phoneNumber)) {

            $errorKeys->errors[$counter] = 3; $counter++;


        } elseif ($isPhoneValid === false) {

            $errorKeys->errors[$counter] = 3; $counter++;


        } elseif ($isDuplicated >= 1) {

            $errorKeys->errors[$counter] = 4; $counter++;

        } elseif ($isDuplicatedTemp >= 1) {

            $errorKeys->errors[$counter] = 14; $counter++;

        } // end if



        // 4: Email
        if (empty($request->newUserData->emailAddress)) {

            $errorKeys->errors[$counter] = 5; $counter++;

        } // end if


        // 5: Password + regionId + stateId
        if (empty($request->newUserData->password)) {

            $errorKeys->errors[$counter] = 17; $counter++;

        } // end if





        // :: Return List
        return $errorKeys;


    } // end function









    // -----------------------------------------------------------------






    protected function checkPhone($userPhone) {

        // ::root
        $userPhone = strval($userPhone);

        
        // 1: check length => NOT CLEAR YET
        return true;


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














    // -----------------------------------------------------------------






    public function resendOTP($userPhone, $lang) {

        // ::root 
        $otpResponse = new stdClass();


        // 1: get Otp-code
        $userLead = UserLead::where('phone', $userPhone)->first();
        $otpCode = $userLead->otp;
    



        // 2: Otp Provider EN / AR




        // ::prepare response
        $otpResponse->otp = $otpCode;

        return $otpResponse;


    } // end function






} // end controller
