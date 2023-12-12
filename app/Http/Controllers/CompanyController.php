<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Traits\AppTrait;

class CompanyController extends Controller {
    

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $companies = Company::all();
        
        return response()->json($companies, 200);

    } // end function


    // ----------------------------------------------------------



    public function store(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $company = new Company();

        $company->name = $request->name;
        $company->nameAr = $request->nameAr;
        $company->serial = $this->createSerial('C', Company::count());
        
        $company->save();

        return response()->json(['status' => true, 'message' => 'Company has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function update(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if


        // ------------------------------------
        // ------------------------------------


        // 1: update item
        $company = Company::find($request->id);

        $company->name = $request->name;
        $company->nameAr = $request->nameAr;
        
        $company->save();

        return response()->json(['status' => true, 'message' => 'Company has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $company = Company::find($id);
        $company->delete();

        return response()->json(['status' => true, 'message' => 'Company has been removed!'], 200);

    } // end function



} // end controller

