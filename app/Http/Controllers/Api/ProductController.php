<?php

namespace App\Http\Controllers\Api;

use App\Models\UserHistory;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use stdClass;


ini_set('max_execution_time', 180); // 180 (seconds) = 3 Minutes


class ProductController extends Controller {
    




    public function searchProducts(Request $request) {

        // 1: get key
        $searchKey = $request->searchKey;
        $contentArray = array();



        // 1.2: check if there is searchKey
        if (!empty($searchKey)) {


            // 1.1: search products / not hidden
            $products = Product::where('name', 'LIKE', "%{$searchKey}%")
            ->orWhere('nameAr', 'LIKE', "%{$searchKey}%")
            ->orderBy('index')
            ->get();

            $products = $products->where('isHidden', false);
        



            foreach ($products as $product) {

                $content = new stdClass();
                $content->id = $product->id;
                $content->categoryId = $product->mainCategoryId;
                $content->subCategoryId = $product->subCategoryId;
                $content->typeId = $product->typeId;
                $content->companyId = $product->companyId;


                $content->name = $product->name;
                $content->nameAr = $product->nameAr;

                $content->mainPic = $product->image;
                $content->additionalPics = null;

                
                
                
                // ::determine productType (byName - fixedSize - dynamicSize)
                $content->productType = $product->weightOption;



                $content->measuringUnitId = $product->unitId;
                $content->minQuantityToOrder = $product->weight;

                $content->quantityAvailable = $product->quantity;
                $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
                $content->originalPrice = $product->sellPrice;
                $content->offerPrice = $product->offerPrice;

                $content->desc = $product->desc;
                $content->descAr = $product->descAr;


                array_push($contentArray, $content);

            } // end loop


        } // end if




        // ::prepare response
        $response = new stdClass();
        $response->searchProducts = $contentArray;


        return response()->json($response, 200);

    } // end function








    // -----------------------------------------------------------------









    public function searchProductsAuth(Request $request) {


        // ::root - get key / phoneId / userId
        $searchKey = $request->searchKey;
        $deviceID = $request->deviceID;
        $userId = auth()->user()->id;


        // 0: create search-history
        $userHistory = new UserHistory();

        $userHistory->userId = $userId;
        $userHistory->deviceID = $deviceID;
        $userHistory->searchKey = $searchKey;

        $userHistory->save();






        // ================================
        // ================================





        // 1: content
        $contentArray = array();



        // 1.2: check if there is searchKey
        if (!empty($searchKey)) {


            // 1.1: search products / not hidden
            $products = Product::where('name', 'LIKE', "%{$searchKey}%")
            ->orWhere('nameAr', 'LIKE', "%{$searchKey}%")
            ->orderBy('index')
            ->get();

            $products = $products->where('isHidden', false);
        


            
            foreach ($products as $product) {

                $content = new stdClass();
                $content->id = $product->id;
                $content->categoryId = $product->mainCategoryId;
                $content->subCategoryId = $product->subCategoryId;
                $content->typeId = $product->typeId;
                $content->companyId = $product->companyId;


                $content->name = $product->name;
                $content->nameAr = $product->nameAr;

                $content->mainPic = $product->image;
                $content->additionalPics = null;

                
                
                
                // ::determine productType (byName - fixedSize - dynamicSize)
                $content->productType = $product->weightOption;


                $content->measuringUnitId = $product->unitId;
                $content->minQuantityToOrder = $product->weight;

                $content->quantityAvailable = $product->quantity;
                $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
                $content->originalPrice = $product->sellPrice;
                $content->offerPrice = $product->offerPrice;

                $content->desc = $product->desc;
                $content->descAr = $product->descAr;


                array_push($contentArray, $content);

            } // end loop


        } // end if




        // ::prepare response
        $response = new stdClass();
        $response->searchProducts = $contentArray;


        return response()->json($response, 200);

    } // end function




} // end controller
