<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Traits\AppTrait;


class SubCategoryController extends Controller {
    

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $subCategories = SubCategory::all();

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
        $subCategory = new SubCategory();

        $subCategory->serial = $this->createSerial('SC', SubCategory::count());
        $subCategory->name = $request->name;
        $subCategory->nameAr = $request->nameAr;
        $subCategory->index = SubCategory::where('mainCategoryId', $request->mainCategoryId)->count() + 1;

        $subCategory->mainCategoryId = $request->mainCategoryId;

        $subCategory->save();

        return response()->json(['status' => true, 'message' => 'SubCategory has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function update(Request $request) {


        // 1: update item
        $subCategory = SubCategory::find($request->id);

        $subCategory->name = $request->name;
        $subCategory->nameAr = $request->nameAr;
        $subCategory->mainCategoryId = $request->mainCategoryId;
        
        $subCategory->save();

        return response()->json(['status' => true, 'message' => 'SubCategory has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $subCategory = SubCategory::find($id);
        $subCategory->delete();

        return response()->json(['status' => true, 'message' => 'SubCategory has been removed!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function sort($mainCategoryId) {

        // 1: get sorted items
        $subCategories = SubCategory::where('mainCategoryId', $mainCategoryId)
        ->orderBy('index','asc')->get();

        return response()->json($subCategories, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateSort(Request $request, $mainCategoryId) {

        // 1: get sortedItems => Ids
        $sortedItems = $request->sortedItems;
        $indexCounter = 1;

        // 1.2: loop thru
        foreach ($sortedItems as $item) {

            $mainCategory = SubCategory::find($item);
            $mainCategory->index = $indexCounter;
            $mainCategory->save();

            $indexCounter++;
        } // end loop




        return response()->json(['message' => 'Items has been sorted!'], 200);

    } // end function



} // end controller
