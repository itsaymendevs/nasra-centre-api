<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DeliveryArea;
use App\Models\Employee;
use App\Models\GeneralBlock;
use App\Models\Order;
use App\Models\PickupStore;
use App\Models\State;
use Illuminate\Http\Request;
use stdClass;

class OrderController extends Controller {



    public function index() {

        // 1: currentOrders
        $orders = Order::with(['user', 'country', 'state', 'deliveryArea', 'store', 'receiver', 'payment', 'orderEmployee', 'paymentEmployee', 'refundEmployee'])
        ->where('orderStatus', '!=', 'COMPLETED')->where('orderStatus', '!=', 'CANCELED')->get();
        
        // ::dependencies
        $countries = Country::all();
        $states = State::all();
        $deliveryAreas = DeliveryArea::all();
        $pickupStores = PickupStore::all();


        // 2: combine
        $combine = new stdClass();
        $combine->orders = $orders;
        $combine->countries = $countries;
        $combine->states = $states;
        $combine->deliveryAreas = $deliveryAreas;
        $combine->stores = $pickupStores;



        return response()->json($combine, 200);
        
    } // end function


    // ----------------------------------------------------------





    public function previousOrders() {

        // 1: currentOrders
        $orders = Order::with(['user', 'country', 'state', 'deliveryArea', 'store', 'receiver', 'payment', 'orderEmployee', 'paymentEmployee', 'refundEmployee'])
        ->where('orderStatus', 'COMPLETED')->orWhere('orderStatus', 'CANCELED')->get();
        
        // ::dependencies
        $countries = Country::all();
        $states = State::all();
        $deliveryAreas = DeliveryArea::all();
        $pickupStores = PickupStore::all();
        $employees = Employee::all();
        $generalBlock = GeneralBlock::all()->first();


        // 2: combine
        $combine = new stdClass();
        $combine->orders = $orders;
        $combine->countries = $countries;
        $combine->states = $states;
        $combine->deliveryAreas = $deliveryAreas;
        $combine->stores = $pickupStores;
        $combine->employees = $employees;
        $combine->generalBlock = $generalBlock;

        

        // ::derived
        $combine->productsTotalPrice = $orders->sum('deliveryPrice');
        $combine->deliveryTotalPrice = $orders->sum('productsPrice');



        return response()->json($combine, 200);
        
    } // end function





    // ----------------------------------------------------------



    
    public function toggleOrdering(Request $request) {


        // 1: get country
        Country::where('code', 'SD')->update([
            'isOrderingActive' => ($request->orderingSD == 'true' || $request->orderingSD == 1) ? false : true
        ]);

        Country::where('code', 'UK')->update([
            'isOrderingActive' => ($request->orderingUK == 'true' || $request->orderingUK == 1) ? false : true
        ]);

        Country::where('code', 'IRL')->update([
            'isOrderingActive' => ($request->orderingIRL == 'true' || $request->orderingIRL == 1) ? false : true
        ]);
    

        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function



    




    // ----------------------------------------------------------



    
    public function toggleGlobalOrdering(Request $request) {


        // 1: get generalBlocks
        $generalBlock = GeneralBlock::all()->first();
        
        $generalBlock->stopOrders = boolval($request->stopOrders);
        $generalBlock->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function




} // end controller
