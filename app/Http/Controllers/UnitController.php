<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Traits\AppTrait;

class UnitController extends Controller {

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $units = Unit::all();

        return response()->json($units, 200);

    } // end function


    // ----------------------------------------------------------



    public function store(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'abbr' => 'required', 'abbrAr' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $unit = new Unit();

        $unit->serial = $this->createSerial('MU', Unit::latest()->first() ? Unit::latest()->first()->id : 0);
        $unit->name = $request->name;
        $unit->nameAr = $request->nameAr;

        $unit->abbr = $request->abbr;
        $unit->abbrAr = $request->abbrAr;

        $unit->save();

        return response()->json(['status' => true, 'message' => 'Unit has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function update(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'abbr' => 'required', 'abbrAr' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if


        // ------------------------------------
        // ------------------------------------


        // 1: update item
        $unit = Unit::find($request->id);

        $unit->name = $request->name;
        $unit->nameAr = $request->nameAr;
        
        $unit->abbr = $request->abbr;
        $unit->abbrAr = $request->abbrAr;


        $unit->save();

        return response()->json(['status' => true, 'message' => 'Unit has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $unit = Unit::find($id);
        $unit->delete();

        return response()->json(['status' => true, 'message' => 'Unit has been removed!'], 200);

    } // end function



} // end controller
