<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MainCategory; 
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;

class MainCategoryController extends Controller {

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $mainCategories = MainCategory::all();
        $category = Category::all()->first();

        $combine = new stdClass();
        $combine->mainCategories = $mainCategories;
        $combine->category = $category;



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
        $mainCategory = new MainCategory();

        $mainCategory->serial = $this->createSerial('MC', MainCategory::count());
        $mainCategory->name = $request->name;
        $mainCategory->nameAr = $request->nameAr;
        $mainCategory->index = MainCategory::count() + 1;
        
        // 1.2: upload image
        if ($request->hasFile('image')) {
            
            $fileName = $this->uploadFile($request, 'image', 'mainCategories/');
            $mainCategory->image = $fileName;

        } // end if



        $mainCategory->save();

        return response()->json(['status' => true, 'message' => 'MainCategory has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function update(Request $request) {

        // 1: update item
        $mainCategory = MainCategory::find($request->id);

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
        $mainCategory = MainCategory::find($id);

        $this->deleteFile($mainCategory->image, 'mainCategories/');
        $mainCategory->delete();

        return response()->json(['status' => true, 'message' => 'MainCategory has been removed!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function sort() {

        // 1: get sorted items
        $mainCategories = MainCategory::orderBy('index','desc')->get();

        return response()->json($mainCategories, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateSort(Request $request) {

        // 1: get sortedItems => Ids
        $sortedItems = $request->sortedItems;
        $indexCounter = 1;

        // 1.2: loop thru
        foreach ($sortedItems as $item) {


            $mainCategory = MainCategory::find($item);
            $mainCategory->index = $indexCounter;
            $mainCategory->save();

            $indexCounter++;
        } // end loop

        return response()->json(['message' => 'Items has been sorted!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updateCovers(Request $request) {

        // 1: update item
        $category = Category::all()->first();


        // 1.2: upload image if exits / remove
        if ($request->hasFile('image')) {
            
            $this->deleteFile($category->image, 'categories/');
            $fileName = $this->uploadFile($request, 'image', 'categories/');
            $category->image = $fileName;

        } // end if



        if ($request->hasFile('imageAr')) {
            
            $this->deleteFile($category->imageAr, 'categories/');
            $fileName = $this->uploadFile($request, 'imageAr', 'categories/');
            $category->imageAr = $fileName;

        } // end if



        $category->save();

        return response()->json(['status' => true, 'message' => 'Category Covers has been updated!'], 200);

    } // end function




} // end function
