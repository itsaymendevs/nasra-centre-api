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
use App\Models\UserReceiver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use stdClass;

class InterOrderController extends Controller
{


    public function makeInterOrder(Request $request)
    {

        // ::root - initials / response / objecting request
        $countryLettersCode = 'SD';
        $countryId = 1;
        $toSDG = 1;

        $response = new stdClass();
        $response->errors = array();

        $request = (Object) $request->all();
        $request->generalInfo = (Object) $request->generalInfo;
        $request->deliveryOrder = (Object) $request->deliveryOrder;
        $request->pickupOrder = (Object) $request->pickupOrder;



        // ::root - generalBlock / receivingOption
        $generalBlock = GeneralBlock::all()->first();
        $receivingOption = 'DELIVERY';



        // ::root - get country / lettersCode - toSDG
        $user = User::find(auth()->user()->id);

        if ($user->country->code != 'SD') {
            $toSDG = doubleval($user->country->toSDG);
            $countryId = $user->country->id;
            $countryLettersCode = $user->country->countryLettersCode;
        } // end if






        // ============================================================
        // ============================================================



        // 1: invalidOrder => [OrderingBlocked - deliveryBlocked or StoreBlocked - InvalidPaymentMethod]



        // 1.1 - user-active
        if (! boolval($user->isActive)) {

            $response->errors[0] = "Unauthorized Access";
            return response()->json($response);

        } // end if




        // 1.2: empty products-list
        if (count($request->generalInfo->orderProducts) == 0) {

            $response->errors[0] = "InvalidOrder";
            return response()->json($response);

        } // end if




        // 1.3: isOrderingBlocked
        if (boolval($generalBlock->stopOrders)) {

            $response = new stdClass();
            $response->unMatchedInformation = new stdClass();
            $response->unMatchedInformation->isOrderingBlocked = true;

            return response()->json($response);

        } // end if








        // 1.4: invalid receiverId
        $receiver = UserReceiver::find($request->generalInfo->receiverId);
        if (empty($receiver)) {

            $response->errors[0] = "Invalid Receiver";
            return response()->json($response);

        } // end if






        // 1.5: invalid toSDG
        if (doubleval($request->generalInfo->toSDG) != $toSDG) {

            $response = new stdClass();
            $response->unMatchedInformation = new stdClass();
            $response->unMatchedInformation->toSDG = $toSDG;

            return response()->json($response);

        } // end if











        // ============================================================
        // ============================================================


        // 2: ReceivingOption => 1: Pickup 2: Delivery



        // 2.1: Pickup Order
        if (! empty($request->pickupOrder)) {

            // ::root
            $receivingOption = 'PICKUP';



            // 2.1.1: PickupBlocked
            if (boolval($generalBlock->stopPickup)) {

                $response = new stdClass();
                $response->unMatchedInformation = new stdClass();
                $response->unMatchedInformation->pdp = $this->mainCall();

                return response()->json($response);

            } // end if



            // 2.1.2: get pickupStore / check isActive
            $pickupStore = PickupStore::find($request->pickupOrder->storeId);

            if (! boolval($pickupStore->isActive)) {

                $response = new stdClass();
                $response->unMatchedInformation = new stdClass();
                $response->unMatchedInformation->pdp = $this->mainCall();

                return response()->json($response);

            } // end if







            // 2.2: Delivery Order
        } else {




            // 2.2.1: DeliveryBlocked
            if (boolval($generalBlock->stopDelivery)) {

                $response = new stdClass();
                $response->unMatchedInformation = new stdClass();
                $response->unMatchedInformation->pdp = $this->mainCall();

                return response()->json($response);

            } // end if






            // 2.2.2: DeliveryAreaBlocked || deliveryPrice != Match
            if (! boolval($receiver->deliveryArea->isActive) || ($receiver->deliveryArea->price != $request->deliveryOrder->deliveryPrice)) {


                // :: return userAddress
                $response = new stdClass();
                $response->unMatchedInformation = new stdClass();
                $response->unMatchedInformation->userAddress = new stdClass();

                $response->unMatchedInformation->userAddress->userStateId = $receiver->stateId;
                $response->unMatchedInformation->userAddress->userRegionId = $receiver->deliveryAreaId;
                $response->unMatchedInformation->userAddress->addressDescription = $receiver->address;

                $response->unMatchedInformation->userAddress->deliveryEstimatedTime = $receiver->deliveryArea->deliveryTime->content;
                $response->unMatchedInformation->userAddress->deliveryEstimatedTimeAr = $receiver->deliveryArea->deliveryTime->contentAr;

                $response->unMatchedInformation->userAddress->regionDeliveryPrice = intval($receiver->deliveryArea->price);
                $response->unMatchedInformation->userAddress->isDeliveryBlocked = ! boolval($receiver->deliveryArea->isActive);


                return response()->json($response);

            } // end if



        } // end else













        // ============================================================
        // ============================================================


        // 3: invalidPaymentOption


        // 3.1: get PaymentMethod
        $paymentType = $request->generalInfo->paymentType;
        $paymentMethod = Payment::find($request->generalInfo->paymentId);





        // 3.2.1: For Delivery
        if ($receivingOption == 'DELIVERY') {

            if (! boolval($paymentMethod->isForDelivery)) {

                $response = new stdClass();
                $response->unMatchedInformation = new stdClass();
                $response->unMatchedInformation->pdp = $this->mainCall();

                return response()->json($response);

            } // end if



            // 3.2.2: For Pickup
        } else {

            if (! boolval($paymentMethod->isForPickup)) {

                $response = new stdClass();
                $response->unMatchedInformation = new stdClass();
                $response->unMatchedInformation->pdp = $this->mainCall();

                return response()->json($response);

            } // end if

        } // end else










        // ============================================================
        // ============================================================




        // 4: outOfStock Products / InvalidPrice Products / Hidden Products


        // ::root - OrderProducts (Array)
        $orderProducts = $request->generalInfo->orderProducts;


        // ::root - Mark Invalid Products
        $productsWithErrors = array_fill(0, count($orderProducts), false);






        // 4.1. HiddenProducts
        $hiddenProducts = array();
        $counter = 0;

        for ($i = 0; $i < count($orderProducts); $i++) {


            // 4.1.1: Objectified
            $orderProducts[$i] = (Object) $orderProducts[$i];





            // 4.1.2: isHidden => Append to HiddenProducts
            $product = Product::find($orderProducts[$i]->id);

            if (boolval($product->isHidden)) {

                $hiddenProducts[$counter] = new stdClass();
                $hiddenProducts[$counter]->id = $product->id;

                $hiddenProducts[$counter]->mainCategoryId = $product->mainCategoryId;
                $hiddenProducts[$counter]->subCategoryId = $product->subCategoryId;
                $hiddenProducts[$counter]->typeId = $product->typeId;

                $hiddenProducts[$counter]->quantityAvailable = doubleval($product->quantity);

                $hiddenProducts[$counter]->originalPrice = doubleval($product->sellPrice);
                $hiddenProducts[$counter]->offerPrice = $product->offerPrice ? doubleval($product->offerPrice) : null;

                $hiddenProducts[$counter]->isHidden = boolval($product->isHidden);




                // ::FlagInErrors / inc. counter
                $counter++;
                $productsWithErrors[$i] = true;

            } // end if



        } //end for loop









        // ------------------------------
        // ------------------------------









        // 4.2: MixedTypes Products
        $mixedProducts = array();


        for ($i = 0; $i < count($orderProducts); $i++) {


            // 4.2.1: Product Not-Flagged
            if ($productsWithErrors[$i] === false) {


                // :: getProduct
                $product = Product::find($orderProducts[$i]->id);



                // ::ProductType
                $currentProductType = $product->weightOption;



                // 4.2.1.1: if Not Same-Type
                if ($currentProductType != $orderProducts[$i]->orderProductType) {


                    // ::Flag / prepare Product
                    $productsWithErrors[$i] = true;


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




                    // ::determine productType
                    $content->productType = $product->weightOption;


                    $content->measuringUnitId = $product->unitId;
                    $content->minQuantityToOrder = $product->weight;

                    $content->quantityAvailable = $product->quantity;
                    $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
                    $content->originalPrice = $product->sellPrice;
                    $content->offerPrice = $product->offerPrice;

                    $content->desc = $product->desc;
                    $content->descAr = $product->descAr;



                    array_push($mixedProducts, $content);

                } // end if


            } // end if

        } //end for loop









        // ------------------------------
        // ------------------------------







        // 4.3: OutOfStock Products
        $outOfStockProducts = array();

        for ($i = 0; $i < count($orderProducts); $i++) {


            // 4.3.1: Product Not-Flagged
            if ($productsWithErrors[$i] === false) {


                // :: getProduct
                $product = Product::find($orderProducts[$i]->id);




                // 4.3.2: orderQuantity > Current Quantity
                if ($orderProducts[$i]->orderProductQuantity > $product->quantity) {


                    // ::Flag / prepare Product
                    $productsWithErrors[$i] = true;


                    $content = new stdClass();
                    $content->id = $product->id;
                    $content->categoryId = $product->mainCategoryId;
                    $content->subCategoryId = $product->subCategoryId;
                    $content->typeId = $product->typeId;


                    $content->quantityAvailable = $product->quantity;
                    $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
                    $content->originalPrice = $product->sellPrice;
                    $content->offerPrice = $product->offerPrice;


                    array_push($outOfStockProducts, $content);


                } // end if

            } // end if

        } // end loop











        // ------------------------------
        // ------------------------------









        // 4.4: invalidPrice Products
        $invalidPriceProducts = array();

        for ($i = 0; $i < count($orderProducts); $i++) {


            // 4.3.1: Product Not-Flagged
            if ($productsWithErrors[$i] === false) {


                // :: getProduct
                $product = Product::find($orderProducts[$i]->id);



                // :: get CurrentPrice
                $currentProductPrice = $product->sellPrice;

                if (! empty($product->offerPrice)) {

                    $currentProductPrice = $product->offerPrice;

                } // end if





                // 4.3.2: orderProductPrice != CurrentPrice in DB
                if ($orderProducts[$i]->orderProductPrice != $currentProductPrice) {


                    // ::Flag / prepare Product
                    $productsWithErrors[$i] = true;


                    $content = new stdClass();
                    $content->id = $product->id;
                    $content->categoryId = $product->mainCategoryId;
                    $content->subCategoryId = $product->subCategoryId;
                    $content->typeId = $product->typeId;


                    $content->quantityAvailable = $product->quantity;
                    $content->maxQuantityToOrder = $product->maxQuantityPerOrder;
                    $content->originalPrice = $product->sellPrice;
                    $content->offerPrice = $product->offerPrice;


                    array_push($invalidPriceProducts, $content);

                } // end if

            } // end if

        } // end loop








        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------








        // 4.5: CHECKING PHASE
        $response = new stdClass();
        $response->reviewOrder = new stdClass();


        // 4.5.1: HiddenProducts
        (count($hiddenProducts) > 0) ? $response->reviewOrder->hiddenProducts = $hiddenProducts : null;



        // 4.5.2: mixedProducts
        (count($mixedProducts) > 0) ? $response->reviewOrder->mixedTypeProducts = $mixedProducts : null;



        // 4.5.3: outOfStockProducts
        (count($outOfStockProducts) > 0) ? $response->reviewOrder->outOfStockProducts = $outOfStockProducts : null;




        // 4.5.4: invalidPriceProducts
        (count($invalidPriceProducts) > 0) ? $response->reviewOrder->invalidPriceProducts = $invalidPriceProducts : null;





        // 4.5.5: return JSON if found
        if (count($hiddenProducts) > 0 || count($mixedProducts) > 0 || count($outOfStockProducts) > 0 || count($invalidPriceProducts) > 0) {

            return response()->json($content);

        } // end if









        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------

        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------


        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------






        // 5: Save Order To DB

        // ::root


        // 1: Generate Serial
        $orderNumber = $this->generateSerial();

        while (true) {

            $isDuplicated = Order::where('orderNumber', $orderNumber)->count();

            if ($isDuplicated == 0)
                break;
            else
                $orderNumber = $this->generateSerial();

        } // end while





        // ------------------------------
        // ------------------------------






        // 2: create Order
        $newOrder = new Order();


        $newOrder->orderNumber = $orderNumber;
        $newOrder->orderLang = $request->generalInfo->orderLang;
        $newOrder->orderDateTime = Carbon::now()->addHours(2);
        $newOrder->orderStatusDateTime = Carbon::now()->addHours(2);
        $newOrder->orderStatus = 'PENDING'; // => WAITING
        $newOrder->orderSecondPhone = $request->generalInfo->secondNumber ? $request->generalInfo->secondNumber : null;
        $newOrder->receivingOption = $receivingOption;


        $newOrder->countryId = $countryId;
        $newOrder->countryLettersCode = $countryLettersCode;
        $newOrder->countryLettersCode = $toSDG;



        // :: receiverInformation
        $newOrder->receiverId = $receiver->id;
        $newOrder->receiverName = $receiver->name;
        $newOrder->receiverPhone = $receiver->phone;
        $newOrder->receiverPhoneAlt = $receiver->phoneAlt;





        // 2.1: DELIVERY / PICKUP
        if ($receivingOption == 'DELIVERY') {


            // 2.1.1: local Receiver
            $newOrder->address = $receiver->address;
            $newOrder->stateId = $receiver->stateId;
            $newOrder->deliveryAreaId = $receiver->deliveryAreaId;


            $newOrder->deliveryPrice = $receiver->deliveryArea->price;
            $newOrder->deliveryEstimatedTime = $receiver->deliveryArea->deliveryTime->content;
            $newOrder->deliveryEstimatedTimeAr = $receiver->deliveryArea->deliveryTime->contentAr;





            // 2.1: PICKUP
        } else {




            // 2.1.1: Generate PickupSerial
            $pickupSerial = $this->generatePickupSerial();

            while (true) {

                $isDuplicated = Order::where('pickupCode', $pickupSerial)->count();

                if ($isDuplicated == 0)
                    break;
                else
                    $pickupSerial = $this->generatePickupSerial();


            } //end of while



            $newOrder->pickupCode = $pickupSerial;
            $newOrder->storeId = $request->pickupOrder->storeId;



        } // end if







        // 2.2: Payments
        $newOrder->paymentType = $paymentType;
        $newOrder->paymentId = $paymentMethod;
        $newOrder->paymentDateTime = null;
        $newOrder->isPaymentDone = false;





        // 2.3: user
        $newOrder->userId = $user->id;




        // 2.4: isConfirmed [Confirmed After Payment]
        $newOrder->isConfirmed = false;


        // ::Save Order
        $newOrder->save();










        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------



        // 3: previousOrderProducts => orderProducts + extraInfo / UpdateProducts (API)
        $productsTotalPrice = 0;
        $updateProducts = array();



        // 3.1: OrderProducts /
        for ($i = 0; $i < count($orderProducts); $i++) {



            // :: getProduct
            $product = Product::find($orderProducts[$i]->id);


            // 1: make OrderProducts
            $newOrderProduct = new OrderProduct();
            $newOrderProduct->orderId = $newOrder->id;
            $newOrderProduct->userId = $user->id;



            // 1.1: ProductInfo
            $newOrderProduct->productId = $product->id;
            $newOrderProduct->serial = $product->serial;
            $newOrderProduct->name = $product->name;
            $newOrderProduct->nameAr = $product->nameAr;
            $newOrderProduct->sellPrice = $product->sellPrice;
            $newOrderProduct->buyPrice = $product->buyPrice;


            $newOrderProduct->weight = $product->weight;
            $newOrderProduct->weightOption = $product->weightOption;
            $newOrderProduct->unitId = $product->unitId;



            if (! empty($product->offerPrice)) {

                $newOrderProduct->sellPrice = $product->offerPrice;

            } // end if




            // 1.2: Quantity / Price
            $newOrderProduct->orderProductQuantity = $orderProducts[$i]->orderProductQuantity;


            // 1.2: TotalPrice based on weightOption
            if ($product->weightOption == 'DYNAMIC') {

                $newOrderProduct->orderProductPrice = ($newOrderProduct->sellPrice * $newOrderProduct->orderProductQuantity) / $product->weight;

                $newOrderProduct->orderBuyProductPrice = ($newOrderProduct->buyPrice * $newOrderProduct->orderProductQuantity) / $product->weight;


                $productsTotalPrice += $newOrderProduct->orderProductPrice;



                // => Fixed / byName
            } else {

                $newOrderProduct->orderProductPrice = ($newOrderProduct->sellPrice * $newOrderProduct->orderProductQuantity);

                $newOrderProduct->orderBuyProductPrice = ($newOrderProduct->buyPrice * $newOrderProduct->orderProductQuantity);


                $productsTotalPrice += $newOrderProduct->orderProductPrice;

            } // end if





            // :: Save orderProduct
            $newOrderProduct->save();



            // :: Take From INVENTORY / Save DB
            $product->quantity = $product->quantity - $newOrderProduct->orderProductQuantity;
            $product->save();





            // :: Add Product into updateProducts
            $content = new stdClass();

            $content->id = $product->id;
            $content->mainCategoryId = $product->mainCategoryId;
            $content->subCategoryId = $product->subCategoryId;
            $content->typeId = $product->typeId;


            $content->quantityAvailable = $product->quantity;
            $content->originalPrice = $product->sellPrice;
            $content->offerPrice = $product->offerPrice;

            array_push($updateProducts, $content);


        } // end loop









        // 3.2: orderTotalPrice / ProductsTotalPrice + DeliveryPrice (optional)
        $orderTotalPrice = $productsTotalPrice;

        if ($receivingOption == "DELIVERY")
            $orderTotalPrice = $productsTotalPrice + doubleval($newOrder->deliveryPrice);




        // :: Save orderTotalPrice / productsPrice
        $newOrder->productsPrice = $productsTotalPrice;
        $newOrder->orderTotalPrice = $orderTotalPrice;

        $newOrder->save();








        // :: CHECK orderTotalPrice / ProductsPrice Mismatch
        $ApiProductsPrice = doubleval($request->generalInfo->productsPrice);
        $ApiOrderTotalPrice = doubleval($request->generalInfo->orderTotalPrice);


        if ($productsTotalPrice != $ApiProductsPrice || $orderTotalPrice != $ApiOrderTotalPrice) {

            // :: Remove Order
            Order::find($newOrder->id)->delete();


            // :: prepare response
            $response = new stdClass();
            $response->errors = array();

            $response->errors[0] = 'invalidOrder';
            $response->productsPrice = $productsTotalPrice;
            $response->orderTotalPrice = $orderTotalPrice;

            return response()->json($content);

        } // end if











        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------
        // ------------------------------











        // ::prepare response
        $response = new stdClass();

        $response->orderNumber = $newOrder->orderNumber;
        $response->paymentURL = route('stripe.makePayment', [$newOrder->serial]);



        return response()->json($content);


    } // end function













    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------




    protected function generateSerial()
    {

        // 1: make 7-Digits
        $serial = mt_rand(1000000, 9999999);
        return $serial;

    } // end function






    // -----------------------------------------------------------------------------------------------





    protected function generatePickupSerial()
    {


        // 1: Formula
        static $max = 60466175;

        // 1.2: Generate
        return strtoupper(sprintf(
            "%06s",
            base_convert(random_int(0, $max), 10, 36),
            base_convert(random_int(0, $max), 10, 36),
            base_convert(random_int(0, $max), 10, 36)
        ));

    } // end function









    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------------------









    public function mainCall()
    {

        // ::root
        $response = new stdClass();
        $response->PickupAndDeliveryAndPaymentInfo = new stdClass();



        // 1: Stores - Pickup Conditions / Delivery Conditions / Ordering Condition / Payment Methods
        $response = $this->PickupDeliveryOrdersDetails($response);
        $response = $this->paymentMethodDetails($response);

        return $response;

    } // end function







    // -----------------------------------------------------------------------------------------------







    protected function paymentMethodDetails($response)
    {

        // ::root
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo = new stdClass();




        // 1: onlineBankingPayments
        $onlineBankingPayments = Payment::where('paymentType', 'ONLINEBANKINGPAYMENT')->get();

        $contentArray = array();
        foreach ($onlineBankingPayments as $onlineBankingPayment) {

            $content = new stdClass();
            $content->id = $onlineBankingPayment->id;
            $content->name = $onlineBankingPayment->name;
            $content->nameAr = $onlineBankingPayment->nameAr;

            $content->accountName = $onlineBankingPayment->accountName;
            $content->accountNumber = $onlineBankingPayment->accountNumber;


            $content->isForDelivery = boolval($onlineBankingPayment->isForDelivery);
            $content->isForPickup = boolval($onlineBankingPayment->isForPickup);
            $content->isForRefund = boolval($onlineBankingPayment->isForRefund);


            array_push($contentArray, $content);

        } // end loop


        // ::push to response
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->onlineBankingPayments = $contentArray;





        // 2: atReceivingPayment
        $atReceivingPayments = Payment::where('paymentType', 'ATRECEIVINGPAYMENT')->get();


        $contentArray = array();
        foreach ($atReceivingPayments as $atReceivingPayment) {

            $content = new stdClass();
            $content->id = $atReceivingPayment->id;
            $content->name = $atReceivingPayment->name;
            $content->nameAr = $atReceivingPayment->nameAr;

            $content->accountName = $atReceivingPayment->accountName;
            $content->accountNumber = $atReceivingPayment->accountNumber;


            $content->isForDelivery = boolval($atReceivingPayment->isForDelivery);
            $content->isForPickup = boolval($atReceivingPayment->isForPickup);
            $content->isForRefund = boolval($atReceivingPayment->isForRefund);


            array_push($contentArray, $content);

        } // end loop



        // ::push to response
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->atReceivingPayments = $contentArray;







        // 3: directPayments
        $directPayments = Payment::where('paymentType', 'DIRECTPAYMENT')->get();


        $contentArray = array();
        foreach ($directPayments as $directPayment) {

            $content = new stdClass();
            $content->id = $directPayment->id;
            $content->name = $directPayment->name;
            $content->nameAr = $directPayment->nameAr;

            $content->accountName = $directPayment->accountName;
            $content->accountNumber = $directPayment->accountNumber;


            $content->isForDelivery = boolval($directPayment->isForDelivery);
            $content->isForPickup = boolval($directPayment->isForPickup);
            $content->isForRefund = boolval($directPayment->isForRefund);


            array_push($contentArray, $content);

        } // end loop



        // ::push to response
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->directPayments = $contentArray;







        // 4: paymentConditions
        $paymentConditions = PaymentCondition::all();


        $contentArray = array();
        foreach ($paymentConditions as $paymentCondition) {

            $content = new stdClass();
            $content->id = $paymentCondition->id;
            $content->title = $paymentCondition->title;
            $content->titleAr = $paymentCondition->titleAr;

            $content->content = $paymentCondition->content;
            $content->contentAr = $paymentCondition->contentAr;

            array_push($contentArray, $content);

        } // end loop



        // ::push to response
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->paymentConditions = $contentArray;




        // :: return response
        return $response;


    } // end function












    // -----------------------------------------------------------------------------------------------








    protected function PickupDeliveryOrdersDetails($response)
    {

        // 1.2: pickup information
        $response->PickupAndDeliveryAndPaymentInfo->pickupInfo = new stdClass();


        // 1.2.1: stores
        $stores = PickupStore::all();

        $contentArray = array();
        foreach ($stores as $store) {

            $content = new stdClass();
            $content->id = $store->id;
            $content->title = $store->title;
            $content->titleAr = $store->titleAr;

            $content->storeLocation = $store->desc;
            $content->storeLocationAr = $store->descAr;

            $content->latitude = $store->latitude;
            $content->longitude = $store->longitude;

            $content->collectingWorkingHours = $store->receivingTimes;
            $content->collectingWorkingHoursAr = $store->receivingTimesAr;

            $content->isPickupAtStoreBlocked = ! boolval($store->isActive);

            array_push($contentArray, $content);

        } // end loop

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->pickupInfo->stores = $contentArray;







        // 1.2.2: conditions
        $conditions = PickupCondition::all();

        $contentArray = array();
        foreach ($conditions as $condition) {

            $content = new stdClass();
            $content->id = $condition->id;
            $content->title = $condition->title;
            $content->titleAr = $condition->titleAr;

            $content->content = $condition->content;
            $content->contentAr = $condition->contentAr;

            array_push($contentArray, $content);

        } // end loop

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->pickupInfo->termsAndConditions = $contentArray;







        // 1.2.3: isPickupBlocked
        $stopPickup = GeneralBlock::all()->first()->stopPickup;

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->pickupInfo->isPickupBlocked = boolval($stopPickup);













        // -----------------------------------------------------------------
        // -----------------------------------------------------------------
        // -----------------------------------------------------------------





        // 1.3: delivery information
        $response->PickupAndDeliveryAndPaymentInfo->deliveryInfo = new stdClass();





        // 1.3.1: conditions
        $conditions = DeliveryCondition::all();

        $contentArray = array();
        foreach ($conditions as $condition) {

            $content = new stdClass();
            $content->id = $condition->id;
            $content->title = $condition->title;
            $content->titleAr = $condition->titleAr;

            $content->content = $condition->content;
            $content->contentAr = $condition->contentAr;

            array_push($contentArray, $content);

        } // end loop

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->deliveryInfo->termsAndConditions = $contentArray;







        // 1.3.2: isDeliveryBlocked
        $stopDelivery = GeneralBlock::all()->first()->stopDelivery;

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->deliveryInfo->isDeliveryBlocked = boolval($stopDelivery);









        // -----------------------------------------------------------------
        // -----------------------------------------------------------------
        // -----------------------------------------------------------------






        // 1.4: isOrderingBlocked
        $stopOrders = GeneralBlock::all()->first()->stopOrders;

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->isOrderingBlocked = boolval($stopOrders);



        return $response;

    } // end function









} // end controller
