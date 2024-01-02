<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentCondition;
use Illuminate\Http\Request;
use App\Traits\AppTrait;

class PaymentController extends Controller {



    // :: use trait
    use AppTrait;


    public function index() {

        // ::dependencies
        $payments = Payment::all();

        return response()->json($payments, 200);

    } // end function


    // ----------------------------------------------------------



    public function store(Request $request) {



        // 1: create item
        $payment = new Payment();

        $payment->serial = $this->createSerial('PM', Payment::latest()->first() ? Payment::latest()->first()->id : 0);

        $payment->paymentType = $request->paymentType;

        $payment->name = $request->name;
        $payment->nameAr = $request->nameAr;
        $payment->accountName = $request->accountName;
        $payment->accountNumber = $request->accountNumber;

        $payment->isForDelivery = boolval($request->isForDelivery);
        $payment->isForPickup = boolval($request->isForPickup);
        $payment->isForRefund = boolval($request->isForRefund);

        $payment->isActive = true;


        $payment->save();

        return response()->json(['status' => true, 'message' => 'Payment has been added!'], 200);


    } // end function




    // ----------------------------------------------------------




    public function update(Request $request) {

        // 1: update item
        $payment = Payment::find($request->id);

        $payment->paymentType = $request->paymentType;

        $payment->name = $request->name;
        $payment->nameAr = $request->nameAr;
        $payment->accountName = $request->accountName;
        $payment->accountNumber = $request->accountNumber;

        $payment->isForDelivery = $request->isForDelivery;
        $payment->isForPickup = $request->isForPickup;
        $payment->isForRefund = $request->isForRefund;

        $payment->isActive = $request->isActive;

        $payment->save();

        return response()->json(['status' => true, 'message' => 'Payment has been updated!'], 200);


    } // end function






    // ----------------------------------------------------------




    public function toggleActive(Request $request, $id) {


        // 1: toggle-active
        $payment = Payment::find($id);

        $payment->isActive = !boolval($payment->isActive);
        $payment->save();

        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);


    } // end function








    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item
        $payment = Payment::find($id);
        $payment->delete();

        return response()->json(['status' => true, 'message' => 'Payment has been removed!'], 200);

    } // end function












    // ===============================================================================
    // ===============================================================================
    // ===============================================================================
    // ===============================================================================
    // ===============================================================================









    public function conditions() {

        // ::get items
        $conditions = PaymentCondition::all();

        return response()->json($conditions, 200);

    } // end function


    // ----------------------------------------------------------



    public function storeCondition(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request,
        ['title' => 'required', 'titleAr' => 'required', 'content' => 'required', 'contentAr' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $condition = new PaymentCondition();

        $condition->serial = $this->createSerial('PC', PaymentCondition::latest()->first() ? PaymentCondition::latest()->first()->id : 0);
        $condition->title = $request->title;
        $condition->titleAr = $request->titleAr;

        $condition->content = $request->content;
        $condition->contentAr = $request->contentAr;

        $condition->save();

        return response()->json(['status' => true, 'message' => 'Condition has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updateCondition(Request $request) {


        // 1: update item
        $condition = PaymentCondition::find($request->id);

        $condition->title = $request->title;
        $condition->titleAr = $request->titleAr;

        $condition->content = $request->content;
        $condition->contentAr = $request->contentAr;

        $condition->save();


        return response()->json(['status' => true, 'message' => 'Condition has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function deleteCondition(Request $request, $id) {

        // 1: delete item / image
        $condition = PaymentCondition::find($id);
        $condition->delete();

        return response()->json(['status' => true, 'message' => 'Condition has been removed!'], 200);

    } // end function






} // end controller
