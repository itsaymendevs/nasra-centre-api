<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;


class SubCategoryController extends Controller {
    

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $subCategories = SubCategory::all();
        $mainCategories = MainCategory::all();

        $combine = new stdClass();
        $combine->mainCategories = $mainCategories;
        $combine->subCategories = $subCategories;

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
        $subCategory = new SubCategory();

        $subCategory->serial = $this->createSerial('SC', SubCategory::latest()->first() ? SubCategory::latest()->first()->id : 0);
        $subCategory->name = $request->name;
        $subCategory->nameAr = $request->nameAr;
        $subCategory->mainCategoryId = $request->mainCategoryId;


        // 1.2: sort SubCategory
        if (true) {

            // 1.2.1: loop thru to sort all again
            $indexCounter = 1;
            $sortSubCategories = SubCategory::where('mainCategoryId', $request->mainCategoryId)
            ->orderBy('index','asc')->get();

            foreach ($sortSubCategories as $item) {

                $item->index = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.2.2: sort recent one
            $subCategory->index = SubCategory::where('mainCategoryId', $request->mainCategoryId)->count() + 1;

        } // end if




        $subCategory->save();

        return response()->json(['status' => true, 'message' => 'SubCategory has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function update(Request $request) {


        // 1: update item
        $subCategory = SubCategory::find($request->id);

        // ::root
        $oldMainCategoryId = $subCategory->mainCategoryId;


        $subCategory->name = $request->name;
        $subCategory->nameAr = $request->nameAr;
        $subCategory->mainCategoryId = $request->mainCategoryId;



        // 1.2: sort SubCategory
        if ($oldMainCategoryId != $request->mainCategoryId) {


            // 1.2.1: loop thru to sort all again
            $indexCounter = 1;
            $sortSubCategories = SubCategory::where('mainCategoryId', $request->mainCategoryId)
            ->orderBy('index','asc')->get();

            foreach ($sortSubCategories as $item) {

                $item->index = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.2.2: sort recent one
            $subCategory->index = SubCategory::where('mainCategoryId', $request->mainCategoryId)->count() + 1;

        } // end if



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
