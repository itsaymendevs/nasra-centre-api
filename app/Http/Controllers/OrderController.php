<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DeliveryArea;
use App\Models\Employee;
use App\Models\GeneralBlock;
use App\Models\GlobalMessage;
use App\Models\Message;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PickupStore;
use App\Models\State;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use stdClass;

class OrderController extends Controller
{



    public function index()
    {

        // 1: currentOrders
        $orders = Order::with(['user', 'user.country', 'country', 'state', 'deliveryArea', 'store', 'receiver', 'payment', 'orderEmployee', 'paymentEmployee', 'refundEmployee'])
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





    public function previousOrders()
    {

        // 1: currentOrders
        $orders = Order::with(['user', 'user.country', 'country', 'state', 'deliveryArea', 'store', 'receiver', 'payment', 'orderEmployee', 'paymentEmployee', 'refundEmployee'])
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




    public function toggleOrdering(Request $request)
    {


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

        Country::where('code', 'EG')->update([
            'isOrderingActive' => ($request->orderingEG == 'true' || $request->orderingEG == 1) ? false : true
        ]);



        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function








    // ----------------------------------------------------------




    public function toggleGlobalOrdering(Request $request)
    {


        // 1: get generalBlocks
        $generalBlock = GeneralBlock::all()->first();

        $generalBlock->stopOrders = boolval($request->stopOrders);
        $generalBlock->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function








    // ----------------------------------------------------------







    public function singleOrder(Request $request, $id)
    {

        // 1: singleOrder
        $order = Order::with(['user.country', 'user.state', 'country', 'state', 'deliveryArea', 'store', 'receiver', 'payment', 'orderEmployee', 'paymentEmployee', 'refundEmployee', 'products', 'receiver.state', 'receiver.deliveryArea'])
            ->where('id', $id)->first();



        // 2: messages / global
        $messages = Message::where('isFor', $order->receivingOption)
            ->where('type', $order->orderStatus)->get();


        if ($order->country->code != 'SD') {
            $messages = GlobalMessage::where('isFor', $order->receivingOption)
                ->where('type', $order->orderStatus)->get();
        } // end if





        // 3: payments
        $payments = null;

        if ($order->receivingOption == 'DELIVERY') {

            $payments = Payment::where('isForDelivery', 1)->where('isActive', 1)->get();

        } else {

            $payments = Payment::where('isForPickup', 1)->where('isActive', 1)->get();

        } // end if






        // 4: combine
        $combine = new stdClass();
        $combine->order = $order;
        $combine->messages = $messages;
        $combine->payments = $payments;




        return response()->json($combine, 200);

    } // end function





    // ----------------------------------------------------------






    public function processOrder(Request $request, $id)
    {

        // 1: updateOrder
        $order = Order::find($id);


        // 1.2: reset cancellation
        $order->refundInvoiceNumber = null;
        $order->orderCancellationNote = null;
        $order->refundEmployeeId = null;
        $order->refundDateTime = null;



        // 1.3: updateStatus

        // 1.3.1: NEXT
        if ($request->action == 'NEXT') {

            if ($order->orderStatus == 'PENDING') {

                $order->orderStatus = 'PROCESSING';

            } else if ($order->orderStatus == 'PROCESSING') {

                $order->orderStatus = 'COMPLETED';

            } // end if



            // 1.3.2: PREVIOUS
        } else {


            if ($order->orderStatus == 'PROCESSING') {

                $order->orderStatus = 'PENDING';

            } // end if


        } // end if





        // 1.4: update DateTime / orderEmployee
        $order->orderStatusDateTime = Carbon::now()->addHours(2);
        $order->orderEmployeeId = 1; // TODO: EMPLOYEE SESSION


        $order->save();



        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);


    } // end function







    // ----------------------------------------------------------






    public function cancelOrder(Request $request, $id)
    {

        // 1: updateOrder
        $order = Order::find($id);


        // 1.2: update cancellation / refund / orderStatus / orderStatusDateTime
        $order->refundInvoiceNumber = ! empty($request->refundInvoiceNumber) ? $request->refundInvoiceNumber : null;
        $order->orderCancellationNote = $request->orderCancellationNote;

        $order->refundEmployeeId = 1; // TODO: EMPLOYEE SESSION
        $order->refundDateTime = Carbon::now()->addHours(2);

        $order->orderStatus = 'CANCELED';



        $order->save();



        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);


    } // end function






    // ----------------------------------------------------------






    public function confirmPayment(Request $request, $id)
    {

        // 1: updateOrder
        $order = Order::find($id);


        // 1.2: get PaymentType
        $payment = Payment::find($request->paymentId);

        // 1.2: update Payment
        $order->paymentNote = null;

        $order->paymentId = $request->paymentId;
        $order->invoiceNumber = $request->invoiceNumber;
        $order->isPaymentDone = true;
        $order->paymentEmployeeId = 1; // TODO: EMPLOYEE SESSION
        $order->paymentDateTime = Carbon::now()->addHours(2);
        $order->paymentType = $payment->paymentType;

        $order->save();




        return response()->json(['status' => true, 'message' => 'Payment has been updated!'], 200);


    } // end function











    // ----------------------------------------------------------






    public function cancelPayment(Request $request, $id)
    {

        // 1: updateOrder
        $order = Order::find($id);

        // 1.2: update Payment
        $order->paymentNote = $request->paymentNote;

        $order->paymentId = null;
        $order->invoiceNumber = null;
        $order->isPaymentDone = false;
        $order->paymentEmployeeId = 1; // TODO: EMPLOYEE SESSION
        $order->paymentDateTime = Carbon::now()->addHours(2);
        $order->paymentType = null;

        $order->save();




        return response()->json(['status' => true, 'message' => 'Payment has been updated!'], 200);


    } // end function











    // ----------------------------------------------------------






    public function sendOTP(Request $request, $id)
    {

        // 1: getOrder
        $order = Order::find($id);


        // 1.2: check if local / global
        $otpType = ! empty($order->receiverId) ? 'global' : 'local';


        // 1.2.1: local
        if ($otpType == 'local') {


            // 1: get userPhone / otpMessage
            $userPhone = $order->user->phone;
            $otpMessage = $request->userOTP;


            // 2: replace userFN / userLN / orderNum / PickupCode (optional)
            $otpMessage = str_replace('@userFN', $order->user->firstName, $otpMessage);
            $otpMessage = str_replace('@userLN', $order->user->lastName, $otpMessage);
            $otpMessage = str_replace('@orderNum', $order->orderNumber, $otpMessage);

            $order->pickupCode ? $otpMessage = str_replace('@pickupCode', $order->pickupCode, $otpMessage) : null;




            // 3: send otp
            $token = env('SMS_TOKEN');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $token
            ])->post('https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', [
                        'from' => 'Nasra', // 11 char max
                        'to' => '00' . $userPhone, // +249 99 959 0002
                        'body' => $otpMessage, // 70 char per message - 160 (latin)
                    ]);








            // 1.2.2: global
        } else {


            // 1: get userPhone / otpMessage
            $userPhone = $order->user->phone;
            $otpMessage = $request->userOTP;

            // 1.2: check if receiver
            if ($request->target == 'receiver') {

                $userPhone = $order->receiverPhone;
                $otpMessage = $request->receiverOTP;

            } // end if




            // 2: replace userFN / userLN / orderNum / receiver /  PickupCode (optional)
            $otpMessage = str_replace('@userFN', $order->user->firstName, $otpMessage);
            $otpMessage = str_replace('@userLN', $order->user->lastName, $otpMessage);
            $otpMessage = str_replace('@orderNum', $order->orderNumber, $otpMessage);
            $otpMessage = str_replace('@receiver', $order->receiverName, $otpMessage);


            $order->pickupCode ? $otpMessage = str_replace('@pickupCode', $order->pickupCode, $otpMessage) : null;




            // 3: send otp
            $token = env('SMS_TOKEN');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $token
            ])->post('https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30', [
                        'from' => 'Nasra', // 11 char max
                        'to' => '00' . $userPhone, // +249 99 959 0002 or +44
                        'body' => $otpMessage, // 70 char per message - 160 (latin)
                    ]);






        } // end if






        return response()->json(['status' => true, 'message' => 'Message has been sent!'], 200);


    } // end function










    // ----------------------------------------------------------






    public function updateOrderNote(Request $request, $id)
    {

        // 1: updateOrder
        $order = Order::find($id);

        // 1.2: update orderNote
        $order->orderNote = $request->orderNote;
        $order->orderStatusDateTime = Carbon::now()->addHours(2);

        $order->save();


        return response()->json(['status' => true, 'message' => 'Note has been updated!'], 200);


    } // end function








} // end controller
