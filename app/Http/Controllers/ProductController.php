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
        $serial = $this->createSerial('P', Product::latest()->first() ? Product::latest()->first()->id : 0);

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

        $product->serial = $this->createSerial('P', Product::latest()->first() ? Product::latest()->first()->id : 0);
        $product->name = $request->name;
        $product->nameAr = $request->nameAr;
        
        $product->buyPrice = $request->buyPrice;
        $product->sellPrice = $request->sellPrice;
        $product->offerPrice = $request->offerPrice;

        $product->desc = $request->desc;
        $product->descAr = $request->descAr;


        // 1.2: weight options
        $product->weightOption = $request->weightOption;

        if ($request->weightOption == 'FIXED' || $request->weightOption == 'DYNAMIC') {
            
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







        // -------------------------------
        // -------------------------------



        // 1.6: indexMainPage - reindex items / reset to null
        if ($product->isMainPage === true) {


            // 1.6.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::where('isMainPage', true)
            ->orderBy('indexMainPage','asc')->get();

            foreach ($sortProducts as $item) {

                $item->indexMainPage = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.6.2: sort recent one
            $product->indexMainPage = Product::where('isMainPage', true)->count() + 1;

        } else {

            $product->indexMainPage = null;
            
        } // end if







        // 1.7: index - reindex items
        if (true) {


            // 1.7.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::where('typeId', $request->typeId)
            ->orderBy('index','asc')->get();

            foreach ($sortProducts as $item) {

                $item->index = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.7.2: sort recent one
            $product->index = Product::where('typeId', $request->typeId)->count() + 1;

        } // end if







        // 1.8: indexOffers - reindex items
        if (!empty($request->offerPrice)) {


            // 1.8.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::whereNotNull('offerPrice')
            ->orderBy('indexOffers','asc')->get();

            foreach ($sortProducts as $item) {

                $item->indexOffers = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.8.2: sort recent one
            $product->indexOffers = Product::whereNotNull('offerPrice')->count() + 1;

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


        // 1: update item
        $product = Product::find($request[0]['id']);

        $product->sellPrice = !empty($request[0]['sellPrice']) ? $request[0]['sellPrice'] : 1;
        $product->offerPrice = !empty($request[0]['offerPrice']) ? $request[0]['offerPrice'] : null;
        $product->quantity =  !empty($request[0]['quantity']) ? $request[0]['quantity'] : 0;





        // 1.2: indexOffers - reindex items
        if (!empty($request[0]['offerPrice']) && empty($product->indexOffers)) {


            // 1.8.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::whereNotNull('offerPrice')
            ->orderBy('indexOffers','asc')->get();

            foreach ($sortProducts as $item) {

                $item->indexOffers = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.8.2: sort recent one
            $product->indexOffers = Product::whereNotNull('offerPrice')->count() + 1;



        } elseif (empty($request[0]['offerPrice'])) {

            $product->indexOffers = null;
            
        } // end if



        $product->save();

        return response()->json(['status' => true, 'message' => 'Product has been updated!'], 200);


    } //end function
    



    // ----------------------------------------------------------



    public function update(Request $request, $id) {

        


        // 1: create item
        $product = Product::find($id);


        // ::deprived
        $oldTypeId = $product->typeId;


        $product->name = $request->name;
        $product->nameAr = $request->nameAr;
        
        $product->buyPrice = $request->buyPrice;
        $product->sellPrice = $request->sellPrice;
        $product->offerPrice = $request->offerPrice;

        $product->desc = $request->desc;
        $product->descAr = $request->descAr;


        // 1.2: weight options
        $product->weightOption = $request->weightOption;

        if ($request->weightOption == 'FIXED' || $request->weightOption == 'DYNAMIC') {
            
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








        // -------------------------------
        // -------------------------------



        // 1.6: indexMainPage - reindex items / reset to null
        if ($product->isMainPage === true && empty($product->indexMainPage)) {


            // 1.6.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::where('isMainPage', true)
            ->orderBy('indexMainPage','asc')->get();

            foreach ($sortProducts as $item) {

                $item->indexMainPage = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.6.2: sort recent one
            $product->indexMainPage = Product::where('isMainPage', true)->count() + 1;



        } elseif ($product->isMainPage === false) {

            $product->indexMainPage = null;
            
        } // end if






        
        // 1.7: index - reindex items
        if ($oldTypeId != $request->typeId) {


            // 1.6.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::where('typeId', $request->typeId)
            ->orderBy('index','asc')->get();

            foreach ($sortProducts as $item) {

                $item->index = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.6.2: sort recent one
            $product->index = Product::where('typeId', $request->typeId)->count() + 1;

        } // end if






        // 1.8: indexOffers - reindex items
        if (!empty($request->offerPrice) && empty($product->indexOffers)) {


            // 1.8.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::whereNotNull('offerPrice')
            ->orderBy('indexOffers','asc')->get();

            foreach ($sortProducts as $item) {

                $item->indexOffers = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.8.2: sort recent one
            $product->indexOffers = Product::whereNotNull('offerPrice')->count() + 1;



        } elseif (empty($request->offerPrice)) {

            $product->indexOffers = null;
            
        } // end if

        



        $product->save();


        return response()->json(['status' => true, 'message' => 'Product has been updated!'], 200);

    } // end function










    // ----------------------------------------------------------




    public function toggleHome(Request $request, $id) {


        // 1: get item
        $product = Product::find($id);
        
        $product->isMainPage = !boolval($product->isMainPage);


        // 1.2: indexMainPage - reindex items / reset to null
        if ($product->isMainPage === true && empty($product->indexMainPage)) {


            // 1.2.1: loop thru to sort all again
            $indexCounter = 1;
            $sortProducts = Product::where('isMainPage', true)
            ->orderBy('indexMainPage','asc')->get();

            foreach ($sortProducts as $item) {

                $item->indexMainPage = $indexCounter;
                $item->save();

                $indexCounter++;

            } // end loop


            // 1.2.2: sort recent one
            $product->indexMainPage = Product::where('isMainPage', true)->count() + 1;



        } elseif ($product->isMainPage === false) {

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
        $product = Product::where('isMainPage', true)->orderBy('indexMainPage','asc')->get();

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
        ->orderBy('index','asc')->get();

        return response()->json($product, 200);

    } // end function




    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $product = Product::find($id);

        // 1.2: remove image if exits
        if ($product->image) {
            
            $this->deleteFile($product->image, 'products/');

        } // end if


        // 1.3: remove firstExtraImage if exits
        if ($product->firstExtraImage) {
            
            $this->deleteFile($product->firstExtraImage, 'products/');

        } // end if


        // 1.2: remove secExtraImage if exits
        if ($product->secExtraImage) {
            
            $this->deleteFile($product->secExtraImage, 'products/');

        } // end if


        // 1.2: remove thirdExtraImage if exits
        if ($product->thirdExtraImage) {
            
            $this->deleteFile($product->thirdExtraImage, 'products/');

        } // end if



        $product->delete();



        return response()->json(['status' => true, 'message' => 'Product has been removed!'], 200);

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
