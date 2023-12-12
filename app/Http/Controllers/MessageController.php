<?php

namespace App\Http\Controllers;

use App\Models\GlobalMessage;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller {


    public function index() {

        // ::get items
        $messages = Message::all();


        return response()->json($messages, 200);

    } // end function




    // ----------------------------------------------------------




    public function update(Request $request) {


        // 1: create item
        $message = Message::find($request->id);

        $message->content = $request->content;
        $message->contentAr = $request->contentAr;

        $message->save();

        return response()->json(['status' => true, 'message' => 'Message has been updated!'], 200);

    } // end function





    // ----------------------------------------------------------




    public function toggleActive(Request $request) {


        // 1: toggle Active
        $message = Message::find($request->id);
        
        $message->isActive = !boolval($message->isActive);
        $message->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function













    // ==========================================================================
    // ==========================================================================
    // ==========================================================================









    public function indexGlobal() {

        // ::get items
        $messages = GlobalMessage::all();


        return response()->json($messages, 200);

    } // end function




    // ----------------------------------------------------------




    public function updateGlobal(Request $request) {


        // 1: create item
        $message = GlobalMessage::find($request->id);

        $message->content = $request->content;
        $message->contentAr = $request->contentAr;

        $message->save();

        return response()->json(['status' => true, 'message' => 'Message has been updated!'], 200);

    } // end function





    // ----------------------------------------------------------




    public function toggleActiveGlobal(Request $request) {


        // 1: toggle Active
        $message = GlobalMessage::find($request->id);
        
        $message->isActive = !boolval($message->isActive);
        $message->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function





} // end controller
