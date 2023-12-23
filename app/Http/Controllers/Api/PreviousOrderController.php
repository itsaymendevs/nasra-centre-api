<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCondition;
use App\Models\GeneralBlock;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\PaymentCondition;
use App\Models\PickupCondition;
use App\Models\PickupStore;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use stdClass;

class PreviousOrderController extends Controller {
    

    public function makeOrder(Request $request) {

        
        // ::root
        $previousOrders = array();
        $user = User::find(auth()->user()->id);




        // 1: PreviousOrders
        $orders = Order::where('userId', $user->id)->get();


        foreach ($orders as $order) {


            $previousOrder = new stdClass();
            $previousOrder->generalInfo = new stdClass();
            $previousOrder->previousOrderProducts = array();

            


            // 1.1: General Info
            $previousOrder->generalInfo->orderNumber = $order->orderNumber;
            $previousOrder->generalInfo->orderDate = date('d-m-Y', strtotime($order->orderDateTime));
            $previousOrder->generalInfo->orderTime = date('h:m i A', strtotime($order->orderDateTime));
            $previousOrder->generalInfo->orderStatus = 'WAITING';
            $previousOrder->generalInfo->paymentType = $order->paymentType;
            $previousOrder->generalInfo->paymentId = $order->paymentId;
            $previousOrder->generalInfo->isPaymentDone = $order->isPaymentDone;





            // 1.2: Products
            $previousOrderProducts = OrderProduct::where('orderId', $order->id)->get();


            foreach ($previousOrderProducts as $previousOrderProduct) {


                $content = new stdClass();
                
                $content->id = $previousOrderProduct->id;
                $content->name = $previousOrderProduct->name;
                $content->nameAr = $previousOrderProduct->nameAr;

                $content->productType = $previousOrderProduct->weightOption;
                $content->packSize = $previousOrderProduct->weight;
                $content->measuringUnitId = $previousOrderProduct->unitId;
                
                $content->orderProductQuantity = $previousOrderProduct->orderProductQuantity;
                $content->orderProductPrice = $previousOrderProduct->orderProductPrice;



                array_push($previousOrder->previousOrderProducts, $content);


            } // end loop











            // 1.3: General Info Part.2
            $previousOrder->generalInfo->productsPrice = doubleval($order->productsPrice);
            $previousOrder->generalInfo->orderTotalPrice = doubleval($order->orderTotalPrice);
            


            

            // 1.4: Receiving Option
            $previousOrder->receivingOption = $receivingOption;


            // 1.4.1: deliveryOrder
            if ($receivingOption == "DELIVERY") {


                $previousOrder->deliveryPreviousOrder = new stdClass();

                $previousOrder->deliveryPreviousOrder->stateDeliveryId = $order->stateId;
                $previousOrder->deliveryPreviousOrder->regionDeliveryId = $order->deliveryAreaId;

                $previousOrder->deliveryPreviousOrder->deliveryEstimatedTime = $order->deliveryEstimatedTime;
                $previousOrder->deliveryPreviousOrder->deliveryEstimatedTimeAr = $order->deliveryEstimatedTimeAr;
                $previousOrder->deliveryPreviousOrder->deliveryPrice = doubleval($order->deliveryPrice);




            // 4.4.2: pickupOrder
            } else {

                $previousOrder->pickupPreviousOrder = new stdClass();

                $previousOrder->pickupPreviousOrder->storeId = $order->storeId;
                $previousOrder->pickupPreviousOrder->pickupCode = $order->pickupCode;


            } // end if






            // ::push to Array
            array_push($previousOrders, $previousOrder);



        } // end loop






        // ::prepare response
        $response = new stdClass();
        $response->previousOrders = $previousOrders;

        
        return response()->json($content); 


    } // end function








} // end controller
