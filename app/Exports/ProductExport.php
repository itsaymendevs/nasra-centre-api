<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use stdClass;



class ProductExport implements FromCollection, WithHeadings
{


    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function headings() : array
    {
        return [
            "Product No.",
            "Name",
            "Hidden",
            "At Home",
            "Company",

            "Main-Category",
            "Sub-Category",
            "Type",

            "Original Price",
            "Main Price",
            "Offer Price",

            "Size/Weight",
            "Remaining Quantity",


            "no. of Favoring",

            "no. of Quantity Ordered",
            "Total Original Orders Price",
            "Total Orders Price",


        ];
    } // end headings









    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function collection()
    {


        // 1: get User
        $combineUsers = array();
        $products = Product::all();


        foreach ($products as $product) {

            $content = new stdClass();

            // 1: general
            $content->id = $product->serial;
            $content->name = $product->name;
            $content->isHidden = boolval($product->isHidden) === true ? 'True' : 'False';
            $content->isMainPage = boolval($product->isMainPage) === true ? 'True' : 'False';

            $content->company = $product->company->name;
            $content->mainCategory = $product->mainCategory->name;
            $content->subCategory = $product->subCategory->name;
            $content->type = $product->type->name;

            $content->buyPrice = $product->buyPrice;
            $content->sellPrice = $product->sellPrice;
            $content->offerPrice = $product->offerPrice;

            $content->weight = $product->weightOption . ($product->weight ? ' / ' . $product->weight : '');
            $content->quantity = $product->quantity;


            $content->favorites = $product->favorites->count() != 0 ? $product->favorites->count() : '0';


            // 2: inOrders => totalQuantity / totalBuyPrice / totalSellPrice
            $content->totalQuantityOrdered = $product->orders->count() != 0 ? $product->orders->sum('orderProductQuantity') : '0';
            $content->totalBuyPriceOrdered = $product->orders->count() != 0 ? $product->orders->sum('orderBuyProductPrice') : '0';
            $content->totalSellPriceOrdered = $product->orders->count() != 0 ? $product->orders->sum('orderProductPrice') : '0';


            array_push($combineUsers, $content);


        } // end loop



        return collect($combineUsers);


    } // end function



} // end export
