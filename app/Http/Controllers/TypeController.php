<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\Type;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;

class TypeController extends Controller {


    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $subCategories = SubCategory::all();
        $mainCategories = MainCategory::all();
        $types = Type::all();


        $combine = new stdClass();
        $combine->mainCategories = $mainCategories;
        $combine->subCategories = $subCategories;
        $combine->types = $types;


        
        return response()->json($combine, 200);

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

        $type->serial = $this->createSerial('IT', Type::latest()->first() ? Type::latest()->first()->id : 0);
        $type->name = $request->name;
        $type->nameAr = $request->nameAr;
        $type->index = Type::where('subCategoryId', $request->subCategoryId)->count() + 1;
        $type->mainCategoryId = $request->mainCategoryId;
        $type->subCategoryId = $request->subCategoryId;
        

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
        
        $type->mainCategoryId = $request->mainCategoryId;
        $type->subCategoryId = $request->subCategoryId;

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
        $types = Type::where('subCategoryId', $subCategoryId)->orderBy('index','desc')->get();

        return response()->json($types, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateSort(Request $request, $mainCategoryId, $subCategoryId) {


        // 1: get sortedItems => Ids
        $sortedItems = $request->sortedItems;
        $indexCounter = 1;

        // 1.2: loop thru
        foreach ($sortedItems as $item) {

            $mainCategory = Type::find($item);
            $mainCategory->index = $indexCounter;
            $mainCategory->save();

            $indexCounter++;
        } // end loop



        return response()->json(['message' => 'Types has been sorted!'], 200);

    } // end function





} // end controller
