<?php

namespace App\Http\Controllers;

use App\Models\GlobalMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use stdClass;

class MessageController extends Controller {


    public function index() {

        // ::get items
        $phoneMessage = Message::where('isFor', 'phone')->first();
        $deliveryMessages = Message::where('isFor', 'delivery')->get();
        $pickupMessages = Message::where('isFor', 'pickup')->get();

        // 1: combine into one object
        $combine = new stdClass();
        $combine->phoneMessage = $phoneMessage;
        $combine->deliveryMessages = $deliveryMessages;
        $combine->pickupMessages = $pickupMessages;

        return response()->json($combine, 200);

    } // end function




    // ----------------------------------------------------------




    public function update(Request $request) {


        // 1: create item
        $message = Message::find($request->id);

        $message->content = $request->content ? $request->content : '';
        $message->contentAr = $request->contentAr ? $request->contentAr : '';

        $message->save();

        return response()->json(['status' => true, 'message' => 'Message has been updated!'], 200);

    } // end function





    // ----------------------------------------------------------




    public function toggleActive(Request $request) {


        // 1: toggle Active
        $message = Message::find($request->id);
        
        $message->isActive = boolval($request->isActive);
        $message->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function













    // ==========================================================================
    // ==========================================================================
    // ==========================================================================









    public function indexGlobal() {

        // ::get items
        $phoneMessageCustomer = GlobalMessage::where('isFor', 'phone')->where('target', 'customer')->first();
        $deliveryMessagesCustomer = GlobalMessage::where('isFor', 'delivery')->where('target', 'customer')->get();
        $pickupMessagesCustomer = GlobalMessage::where('isFor', 'pickup')->where('target', 'customer')->get();

       
        $deliveryMessagesReceiver = GlobalMessage::where('isFor', 'delivery')->where('target', 'receiver')->get();
        $pickupMessagesReceiver = GlobalMessage::where('isFor', 'pickup')->where('target', 'receiver')->get();


        // 1: combine into one object
        $combine = new stdClass();
        $combine->phoneMessageCustomer = $phoneMessageCustomer;
        $combine->deliveryMessagesCustomer = $deliveryMessagesCustomer;
        $combine->pickupMessagesCustomer = $pickupMessagesCustomer;

        $combine->deliveryMessagesReceiver = $deliveryMessagesReceiver;
        $combine->pickupMessagesReceiver = $pickupMessagesReceiver;

        return response()->json($combine, 200);

    } // end function




    // ----------------------------------------------------------




    public function updateGlobal(Request $request) {


        // 1: create item
        $message = GlobalMessage::find($request->id);

        $message->content = $request->content ? $request->content : '';
        $message->contentAr = $request->contentAr ? $request->contentAr : '';

        $message->save();

        return response()->json(['status' => true, 'message' => 'Message has been updated!'], 200);

    } // end function





    // ----------------------------------------------------------




    public function toggleActiveGlobal(Request $request) {


        // 1: toggle Active
        $message = GlobalMessage::find($request->id);
        
        $message->isActive = boolval($request->isActive);
        $message->save();


        return response()->json(['status' => $request->id, 'message' => 'Status has been updated!'], 200);

    } // end function





} // end controller
