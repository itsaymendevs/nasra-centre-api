<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\MainCategory;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Type;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;

class ProductController extends Controller {
    

    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $products = Product::all();
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $types = Type::all();
        $companies = Company::all();

        $combine = new stdClass();
        $combine->mainCategories = $mainCategories;
        $combine->subCategories = $subCategories;
        $combine->types = $types;
        $combine->products = $products;
        $combine->companies = $companies;


        return response()->json($combine, 200);

    } // end function





    // ----------------------------------------------------------






    public function create() {

        // ::get items
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $types = Type::all();
        $companies = Company::all();
        $units = Unit::all();
        $serial = $this->createSerial('P', Product::count());

        $combine = new stdClass();
        $combine->mainCategories = $mainCategories;
        $combine->subCategories = $subCategories;
        $combine->types = $types;
        $combine->units = $units;
        $combine->serial = $serial;
        $combine->companies = $companies;


        return response()->json($combine, 200);

    } // end function





    // ----------------------------------------------------------



    public function store(Request $request) {


        // 1: create item
        $product = new Product();

        $product->serial = $this->createSerial('P', Product::count());
        $product->name = $request->name;
        $product->nameAr = $request->nameAr;
        
        $product->buyPrice = $request->buyPrice;
        $product->sellPrice = $request->sellPrice;
        $product->offerPrice = $request->offerPrice;

        $product->desc = $request->desc;
        $product->descAr = $request->descAr;


        // 1.2: weight options
        $product->weightOption = $request->weightOption;

        if ($request->weightOption == 'fixedSize' || $request->weightOption == 'dynamicSize') {
            
            $product->weight = $request->weight;
            $product->unitId = $request->unitId;

        } // end if

        $product->units = $request->units;
        $product->quantityPerUnit = $request->quantityPerUnit;
        $product->quantity = $request->units * $request->quantityPerUnit;
        $product->maxQuantityPerOrder = $request->maxQuantityPerOrder;
        
        $product->isHidden = $request->isHidden == 'true' ? true : false;
        $product->isMainPage = $request->isMainPage == 'true' ? true: false;

        $product->companyId = $request->companyId;
        $product->mainCategoryId = $request->mainCategoryId;
        $product->subCategoryId = $request->subCategoryId;
        $product->typeId = $request->typeId;
        

        // 1.3: category index based on type
        $product->index = Product::where('typeId', $request->typeId)->count() + 1;




        // 1.4: upload image if exits
        if ($request->hasFile('image')) {

            $fileName = $this->uploadFile($request, 'image', 'products/');
            $product->image = $fileName;

        } // end if





        // 1.5: upload extra-image if exits
        if ($request->hasFile('firstExtraImage')) {

            $fileName = $this->uploadFile($request, 'firstExtraImage', 'products/');
            $product->firstExtraImage = $fileName;

        } // end if


        if ($request->hasFile('secExtraImage')) {

            $fileName = $this->uploadFile($request, 'secExtraImage', 'products/');
            $product->secExtraImage = $fileName;

        } // end if


        if ($request->hasFile('thirdExtraImage')) {

            $fileName = $this->uploadFile($request, 'thirdExtraImage', 'products/');
            $product->thirdExtraImage = $fileName;

        } // end if


        $product->save();


        return response()->json(['status' => true, 'message' => 'Product has been added!'], 200);

    } // end function





    // ---------------------------------------------------------





    public function edit($id) {

        // ::get items
        $product = Product::find($id);
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $types = Type::all();
        $companies = Company::all();
        $units = Unit::all();
        

        $combine = new stdClass();
        $combine->product = $product;
        $combine->mainCategories = $mainCategories;
        $combine->subCategories = $subCategories;
        $combine->types = $types;
        $combine->units = $units;
        $combine->companies = $companies;


        return response()->json($combine, 200);

    } // end function









    // ----------------------------------------------------------



    public function updateShorthand(Request $request) {


        // 1: create item

        $product = Product::find($request[0]['id']);

        $product->sellPrice = !empty($request[0]['sellPrice']) ? $request[0]['sellPrice'] : 1;
        $product->offerPrice = !empty($request[0]['offerPrice']) ? $request[0]['offerPrice'] : null;
        $product->quantity =  !empty($request[0]['quantity']) ? $request[0]['quantity'] : 0;

        $product->save();

        return response()->json(['status' => true, 'message' => 'Product has been updated!'], 200);


    } //end function
    



    // ----------------------------------------------------------



    public function update(Request $request, $id) {


        // 1: create item
        $product = Product::find($id);

        $product->name = $request->name;
        $product->nameAr = $request->nameAr;
        
        $product->buyPrice = $request->buyPrice;
        $product->sellPrice = $request->sellPrice;
        $product->offerPrice = $request->offerPrice;

        $product->desc = $request->desc;
        $product->descAr = $request->descAr;


        // 1.2: weight options
        $product->weightOption = $request->weightOption;

        if ($request->weightOption == 'fixedSize' || $request->weightOption == 'dynamicSize') {
            
            $product->weight = $request->weight;
            $product->unitId = $request->unitId;

        } else {

            $product->weight = null;
            $product->unitId = null;

        } // end if

        $product->units = $request->units;
        $product->quantityPerUnit = $request->quantityPerUnit;
        $product->quantity = $request->units * $request->quantityPerUnit;
        $product->maxQuantityPerOrder = $request->maxQuantityPerOrder;
        
        $product->isHidden = $request->isHidden == 'true' ? true : false;
        $product->isMainPage = $request->isMainPage == 'true' ? true: false;

        $product->companyId = $request->companyId;
        $product->mainCategoryId = $request->mainCategoryId;
        $product->subCategoryId = $request->subCategoryId;
        $product->typeId = $request->typeId;
        

        // 1.3: category index based on type
        $product->index = Product::where('id', '!=', $product->id)
        ->where('typeId', $request->typeId)->count() + 1;



        // 1.2: upload image if exits
        if ($request->hasFile('image')) {
            
            $this->deleteFile($product->image, 'products/');

            $fileName = $this->uploadFile($request, 'image', 'products/');
            $product->image = $fileName;

        } // end if




        // 1.5: upload extra-image if exits
        if ($request->hasFile('firstExtraImage')) {

            $this->deleteFile($product->firstExtraImage, 'products/');

            $fileName = $this->uploadFile($request, 'firstExtraImage', 'products/');
            $product->firstExtraImage = $fileName;

        } // end if


        if ($request->hasFile('secExtraImage')) {

            $this->deleteFile($product->secExtraImage, 'products/');

            $fileName = $this->uploadFile($request, 'secExtraImage', 'products/');
            $product->secExtraImage = $fileName;

        } // end if


        if ($request->hasFile('thirdExtraImage')) {

            $this->deleteFile($product->thirdExtraImage, 'products/');

            $fileName = $this->uploadFile($request, 'thirdExtraImage', 'products/');
            $product->thirdExtraImage = $fileName;

        } // end if





        $product->save();


        return response()->json(['status' => true, 'message' => 'Product has been updated!'], 200);

    } // end function










    // ----------------------------------------------------------




    public function toggleHome(Request $request, $id) {


        // 1: get item
        $product = Product::find($id);
        
        $product->isMainPage = !boolval($product->isMainPage);


        // reindex items / reset to null
        if ($product->isMainPage === true) {


            // 1.3: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::where('isMainPage', true)
            ->orderBy('indexMainPage','desc')->get();

            foreach ($sortProducts as $item) {

                $item->indexMainPage = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.2: sort recent one
            $product->indexMainPage = Product::where('isMainPage', true)->count() + 1;

        } else {

            $product->indexMainPage = null;
            
        } // end if



        // update in database
        $product->save();



        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function









    // ----------------------------------------------------------




    public function toggleHidden(Request $request, $id) {


        // 1: get item
        $product = Product::find($id);
        
        $product->isHidden = !boolval($product->isHidden);
        $product->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function



    // ----------------------------------------------------------



    public function mainPageSort() {

        // 1: get sorted items
        $product = Product::where('isMainPage', true)->orderBy('indexMainPage','desc')->get();

        return response()->json($product, 200);

    } // end function








    // ----------------------------------------------------------



    public function updateMainPageSort(Request $request) {

        // 1: get sortedItems => Ids
        $sortedItems = $request->sortedItems;
        $indexCounter = 1;

        // 1.2: loop thru
        foreach ($sortedItems as $item) {

            $product = Product::find($item);
            $product->indexMainPage = $indexCounter;
            $product->save();

            $indexCounter++;
        } // end loop


        return response()->json(['message' => 'Items has been sorted!'], 200);

        
    } // end function






    

    // ----------------------------------------------------------



    public function typeSort($typeId) {

        // 1: get sorted items
        $product = Product::where('typeId', $typeId)
        ->orderBy('index','desc')->get();

        return response()->json($product, 200);

    } // end function




    // ----------------------------------------------------------



    public function updateTypeSort(Request $request, $typeId) {

        // 1: get sortedItems => Ids
        $sortedItems = $request->sortedItems;
        $indexCounter = 1;

        // 1.2: loop thru
        foreach ($sortedItems as $item) {

            $product = Product::find($item);
            $product->index = $indexCounter;
            $product->save();

            $indexCounter++;
        } // end loop




        return response()->json(['message' => 'Items has been sorted!'], 200);

    } // end function




} // end controller
