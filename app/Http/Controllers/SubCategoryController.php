<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Traits\AppTrait;


class SubCategoryController extends Controller {
    

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $subCategories = Subcategory::all();

        return response()->json($subCategories, 200);

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
        $subCategory = new Subcategory();

        $subCategory->serial = $this->createSerial('SC', Subcategory::count());
        $subCategory->name = $request->name;
        $subCategory->nameAr = $request->nameAr;
        $subCategory->index = Subcategory::count() + 1;

        $subCategory->maincategory_id = $request->mainCategoryId;

        $subCategory->save();

        return response()->json(['status' => true, 'message' => 'SubCategory has been added!'], 200);

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
        $subCategory = Subcategory::find($request->id);

        $subCategory->name = $request->name;
        $subCategory->nameAr = $request->nameAr;
        $subCategory->maincategory_id = $request->mainCategoryId;

        
        $subCategory->save();

        return response()->json(['status' => true, 'message' => 'SubCategory has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $subCategory = Subcategory::find($id);
        $subCategory->delete();

        return response()->json(['status' => true, 'message' => 'SubCategory has been removed!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function sort($mainCategoryId) {

        // 1: get sorted items
        $subCategories = Subcategory::where('maincategory_id', $mainCategoryId)
        ->orderBy('index','asc')->get();

        return response()->json($subCategories, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateSort(Request $request, $mainCategoryId) {

        return response()->json(['message' => 'Items has been sorted!'], 200);

    } // end function



} // end controller
