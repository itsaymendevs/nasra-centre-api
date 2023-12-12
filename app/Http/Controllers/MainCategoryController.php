<?php

namespace App\Http\Controllers;

use App\Models\Maincategory; 
use Illuminate\Http\Request;
use App\Traits\AppTrait;

class MainCategoryController extends Controller {

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $mainCategories = Maincategory::all();

        return response()->json($mainCategories, 200);

    } // end function


    // ----------------------------------------------------------



    public function store(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'image' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $mainCategory = new Maincategory();

        $mainCategory->serial = $this->createSerial('MC', Maincategory::count());
        $mainCategory->name = $request->name;
        $mainCategory->nameAr = $request->nameAr;
        $mainCategory->index = Maincategory::count() + 1;
        
        // 1.2: upload image
        $fileName = $this->uploadFile($request, 'image', 'mainCategories/');
        $mainCategory->image = $fileName;

        $mainCategory->save();

        return response()->json(['status' => true, 'message' => 'MainCategory has been added!'], 200);

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
        $mainCategory = Maincategory::find($request->id);

        $mainCategory->name = $request->name;
        $mainCategory->nameAr = $request->nameAr;
        
        // 1.2: upload image if exits / remove
        if ($request->hasFile('image')) {
            
            $this->deleteFile($mainCategory->image, 'mainCategories/');
            $fileName = $this->uploadFile($request, 'image', 'mainCategories/');
            $mainCategory->image = $fileName;

        } // end if



        $mainCategory->save();

        return response()->json(['status' => true, 'message' => 'MainCategory has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $mainCategory = Maincategory::find($id);

        $this->deleteFile($mainCategory->image, 'mainCategories/');
        $mainCategory->delete();

        return response()->json(['status' => true, 'message' => 'MainCategory has been removed!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function sort() {

        // 1: get sorted items
        $mainCategories = Maincategory::orderBy('index','asc')->get();

        return response()->json($mainCategories, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateSort(Request $request) {

        return response()->json(['message' => 'Items has been sorted!'], 200);

    } // end function


} // end function
