<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use App\Traits\AppTrait;

class TypeController extends Controller {


    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $types = Type::all();

        return response()->json($types, 200);

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
        $type = new Type();

        $type->serial = $this->createSerial('IT', Type::count());
        $type->name = $request->name;
        $type->nameAr = $request->nameAr;
        $type->index = Type::count() + 1;
        
        $type->maincategory_id = $request->mainCategoryId;
        $type->subcategory_id = $request->subCategoryId;
        

        $type->save();

        return response()->json(['status' => true, 'message' => 'Type has been added!'], 200);

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
        $type = Type::find($request->id);

        $type->name = $request->name;
        $type->nameAr = $request->nameAr;
        
        $type->maincategory_id = $request->mainCategoryId;
        $type->subcategory_id = $request->subCategoryId;

        $type->save();

        return response()->json(['status' => true, 'message' => 'Type has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $type = Type::find($id);
        $type->delete();

        return response()->json(['status' => true, 'message' => 'Type has been removed!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function sort($mainCategoryId, $subCategoryId) {

        // 1: get sorted items
        $types = Type::where('subcategory_id', $subCategoryId)->orderBy('index','asc')->get();

        return response()->json($types, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateSort(Request $request, $mainCategoryId, $subCategoryId) {

        return response()->json(['message' => 'Types has been sorted!'], 200);

    } // end function





} // end controller
