<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use stdClass;

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
                if ($product->weightOption == 'byName')
                    $content->productType = 'NAMEFULL';

                else if ($product->weightOption == 'fixedSize')
                    $content->productType = 'FIXED';

                else
                    $content->productType = 'DYNAMIC';


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


        // TODO: Make Authentication + Save History

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
                if ($product->weightOption == 'byName')
                    $content->productType = 'NAMEFULL';

                else if ($product->weightOption == 'fixedSize')
                    $content->productType = 'FIXED';

                else
                    $content->productType = 'DYNAMIC';


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
