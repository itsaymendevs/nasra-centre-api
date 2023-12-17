<?php

namespace App\Http\Controllers;

use App\Models\DeliveryArea;
use App\Models\DeliveryCondition;
use App\Models\DeliveryTime;
use App\Models\District;
use App\Models\GeneralBlock;
use App\Models\State;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;

class DeliveryController extends Controller {



    // :: use trait
    use AppTrait;


    public function index() {


        // ::get items
        $areas = DeliveryArea::all();
        $stopDelivery = GeneralBlock::all()->first()->stopDelivery;
        $states = State::all();
        $districts = District::all();
        $deliveryTimes = DeliveryTime::all();

        $combine = new stdClass();
        $combine->stopDelivery = $stopDelivery;
        $combine->areas = $areas;
        $combine->states = $states;
        $combine->districts = $districts;
        $combine->deliveryTimes = $deliveryTimes;

        return response()->json($combine, 200);

    } // end function






    // ----------------------------------------------------------




    public function toggleDelivery(Request $request) {


        // 1: toggle Receiving pickups
        $generalBlock = GeneralBlock::all()->first();
        
        $generalBlock->stopDelivery = boolval($request->stopDelivery);
        $generalBlock->save();


        return response()->json(['status' => true, 'message' => 'Delivery Blocking has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------



    public function store(Request $request) {


        // 1: create item
        $area = new DeliveryArea();

        $area->serial = $this->createSerial('DA', DeliveryArea::count());
        $area->name = $request->name;
        $area->nameAr = $request->nameAr;
        $area->price = $request->price;

        $area->stateId = $request->stateId;
        $area->districtId = $request->districtId;
        $area->deliveryTimeId = $request->deliveryTimeId;

        $area->isActive = !boolval($request->isActive);

        $area->save();


        return response()->json(['status' => true, 'message' => 'Area has been added!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function edit(Request $request, $id) {


        // 1: get pickup
        $area = DeliveryArea::find($id);
        $states = State::all();
        $districts = District::all();
        $deliveryTimes = DeliveryTime::all();

        $combine = new stdClass();
        $combine->area = $area;
        $combine->states = $states;
        $combine->districts = $districts;
        $combine->deliveryTimes = $deliveryTimes;


        return response()->json($combine, 200);

    } // end function



    // ----------------------------------------------------------



    public function update(Request $request, $id) {

        // 1: create item
        $area = DeliveryArea::find($id);

        $area->name = $request->name;
        $area->nameAr = $request->nameAr;
        $area->price = $request->price;

        $area->stateId = $request->stateId;
        $area->districtId = $request->districtId;
        $area->deliveryTimeId = $request->deliveryTimeId;

        $area->isActive = !boolval($request->isActive);

        $area->save();


        return response()->json(['status' => true, 'message' => 'Area has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function toggleActive(Request $request, $id) {


        // 1: get area
        $area = DeliveryArea::find($id);
        
        $area->isActive = !boolval($area->isActive);
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
