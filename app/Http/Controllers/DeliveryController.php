<?php

namespace App\Http\Controllers;

use App\Models\DeliveryArea;
use App\Models\DeliveryCondition;
use App\Models\GeneralBlock;
use Illuminate\Http\Request;
use App\Traits\AppTrait;

class DeliveryController extends Controller {



    // :: use trait
    use AppTrait;


    public function index() {

        // ::get items
        $areas = DeliveryArea::all();
        
        return response()->json($areas, 200);

    } // end function






    // ----------------------------------------------------------




    public function toggleDelivery() {


        // 1: toggle Receiving pickups
        $generalBlock = GeneralBlock::all()->first();
        
        $generalBlock->stopDelivery = !boolval($generalBlock->stopDelivery);
        $generalBlock->save();


        return response()->json(['status' => true, 'message' => 'Delivery Blocking has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------



    public function store(Request $request) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'price' => 'required', 'state_id' => 'required', 'district_id' => 'required']);
        
        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $area = new DeliveryArea();

        $area->serial = $this->createSerial('DA', DeliveryArea::count());
        $area->name = $request->name;
        $area->nameAr = $request->nameAr;
        $area->price = $request->price;

        $area->state_id = $request->state_id;
        $area->district_id = $request->district_id;
        $area->delivery_time_id = $request->delivery_time_id;

        $area->isActive = $request->isActive ? false : true;

        $area->save();


        return response()->json(['status' => true, 'message' => 'Area has been added!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function edit(Request $request, $id) {


        // 1: get pickup
        $area = DeliveryArea::find($id);

        return response()->json($area, 200);

    } // end function



    // ----------------------------------------------------------



    public function update(Request $request, $id) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'price' => 'required', 'state_id' => 'required', 'district_id' => 'required']);
        
        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $area = DeliveryArea::find($id);

        $area->name = $request->name;
        $area->nameAr = $request->nameAr;
        $area->price = $request->price;

        $area->state_id = $request->state_id;
        $area->district_id = $request->district_id;
        $area->delivery_time_id = $request->delivery_time_id;

        $area->isActive = $request->isActive ? false : true;

        $area->save();


        return response()->json(['status' => true, 'message' => 'Area has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function toggleActive(Request $request, $id) {


        // 1: get area
        $area = DeliveryArea::find($id);
        
        $area->isActive = !boolval($request->isActive);
        $area->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------



    public function delete(Request $request, $id) {

        // 1: delete item / image
        $area = DeliveryArea::find($id);
        $area->delete();

        return response()->json(['status' => true, 'message' => 'Area has been removed!'], 200);

    } // end function




    


    // ===============================================================================
    // ===============================================================================
    // ===============================================================================
    // ===============================================================================
    // ===============================================================================









    public function conditions() {

        // ::get items
        $conditions = DeliveryCondition::all();

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
        $condition = new DeliveryCondition();

        $condition->serial = $this->createSerial('DC', DeliveryCondition::count());
        $condition->title = $request->title;
        $condition->titleAr = $request->titleAr;

        $condition->content = $request->content;
        $condition->contentAr = $request->contentAr;

        $condition->save();

        return response()->json(['status' => true, 'message' => 'Condition has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updateCondition(Request $request) {

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
        $condition = DeliveryCondition::find($request->id);

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
        $condition = DeliveryCondition::find($id);
        $condition->delete();

        return response()->json(['status' => true, 'message' => 'Condition has been removed!'], 200);

    } // end function



} // end controller
