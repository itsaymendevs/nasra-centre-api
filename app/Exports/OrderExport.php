<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use stdClass;

class OrderExport implements FromCollection, WithHeadings
{


    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function headings() : array
    {
        return [
            "Order No.",
            "Order DateTime",
            "Order Status",
            "no. of Products",


            "Products Original Price",
            "Products Sell Price",

            "Order Total Price",
            "Receiving Option",

            // 1.1: pickup
            "Pickup Store",
            "Receipt Code",



            // 1.2: delivery
            "Delivery State",
            "Delivery Region",
            "Delivery Price",


            "Customer Name",
            "Phone Number",
            "Second Phone Number",

            "Payment Status",
            "Payment Type",
            "Payment Method",

            "Invoice/Receipt Number",
            "Resp Employee to Order",
            "Order Completion/Cancellation DateTime",

            "Resp Employee for Payment",
            "Payment Date and Time",
            "Order Notes",



            // 2: Refunds
            "Resp Employee for Cancel and Refund",
            "Refund Date and Time",
            "Refund Invoice/Receipt Number",
            "Cancellation Note",



        ];
    } // end headings









    // ------------------------------------------------------------
    // ------------------------------------------------------------



    public function collection()
    {


        // 1: get localOrders
        $combineUsers = array();
        $localOrders = Order::where('countryId', 1)->get();


        foreach ($localOrders as $order) {

            $content = new stdClass();

            // 1: general
            $content->id = $order->serial;
            $content->orderDateTime = date('d-M-Y h:i A', strtotime($order->orderDateTime));
            $content->orderStatus = $order->orderStatus;

            $content->totalProducts = $order->products->count() != 0 ? $order->products->count() : '0';
            $content->productsBuyPrice = $order->products->count() != 0 ? $order->products->sum('orderBuyProductPrice') : '0';
            $content->productsSellPrice = $order->productsPrice;
            $content->orderTotalPrice = $order->orderTotalPrice;


            // 1.2: receivingOptions
            $content->receivingOption = $order->receivingOption;

            if ($order->receivingOption == 'PICKUP') {

                $content->pickupStore = $order->store->title;
                $content->receiptCode = $order->pickupCode;


            } else {


                $content->deliveryState = $order->state->name;
                $content->deliveryRegion = $order->deliveryArea->name;
                $content->deliveryPrice = $order->deliveryPrice;


            } // end if




            // 1.2: customer
            $content->customerName = $order->user->firstName . ' ' . $order->user->lastName;
            $content->phoneNumber = $order->user->phone;
            $content->secondPhoneNumber = $order->orderSecondPhone;



            // 1.3: payment
            $content->paymentStatus = boolval($order->isPaymentDone) === true ? 'Paid' : 'Not Paid';
            $content->paymentType = boolval($order->isMainPage) === true ? $order->paymentType : '';
            $content->paymentMethod = (boolval($order->isMainPage) === true && ! empty($order->paymentId)) ? $order->payment->name : '';
            $content->invoiceNumber = $order->invoiceNumber;


            $content->orderEmployee = $order->orderEmployeeId ? $order->orderEmployee->name : '';
            $content->orderStatusDateTime = $order->orderStatusDateTime;

            $content->paymentEmployee = $order->paymentEmployeeId ? $order->paymentEmployee->name : '';
            $content->paymentDateTime = $order->paymentDateTime;
            $content->orderNote = $order->orderNote;



            // 1.4: refund
            $content->refundEmployee = $order->refundEmployeeId ? $order->refundEmployee->name : '';
            $content->refundDateTime = $order->refundDateTime;
            $content->refundInvoiceNumber = $order->refundInvoiceNumber;
            $content->orderCancellationNote = $order->orderCancellationNote;



            array_push($combineUsers, $content);


        } // end loop



        return collect($combineUsers);


    } // end function



} // end export



