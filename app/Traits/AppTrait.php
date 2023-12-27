<?php

namespace App\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

trait AppTrait {

    // 1: validate by rules
    protected function validationTrait(Request $request, array $rules) {

        $validator = Validator::make($request->all(), $rules);


        // 1: failed
        if ($validator->fails()) {

            return response()->json($validator->messages());

        } // end if


        // 2: passed
        return false;
        
    } // end function




    // --------------------------------------------------------------



    protected function uploadFile(Request $request, $name, $path) {

        $file = $request->file($name); // Retrieve the uploaded file from the request
        $fileName = date("h.iA") . $file->getClientOriginalName(); // Retrieve the original filename

        Storage::disk('public')->put($path . '' . $fileName, file_get_contents($file));

        return $fileName;

    } // end function




    // --------------------------------------------------------------



    protected function deleteFile($name, $path) {

        Storage::disk('public')->delete($path . '' . $name);
        return true;
    } // end function



    // --------------------------------------------------------------


    protected function createSerial($characters, $number) {

        $number = intval($number);

        
        if ($number < 10) {

            return $characters .'-00'. ($number + 1);

        } elseif ($number < 100) {

            return $characters .'-0'. ($number + 1);

        } elseif ($number < 1000) {

            return $characters .'-'. ($number + 1);

        } // end if


    } // end function


} // end trait