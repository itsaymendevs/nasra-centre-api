<?php

namespace App\Http\Controllers;

use App\Models\GeneralBlock;
use App\Models\PickupCondition;
use App\Models\PickupStore;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;


class PickupController extends Controller
{


    // :: use trait
    use AppTrait;


    public function index()
    {

        // ::get items
        $pickups = PickupStore::all();
        $stopPickup = GeneralBlock::all()->first()->stopPickup;

        $combine = new stdClass();
        $combine->stopPickup = $stopPickup;
        $combine->pickups = $pickups;

        return response()->json($combine, 200);

    } // end function






    // ----------------------------------------------------------




    public function toggleReceiving(Request $request)
    {


        // 1: toggle Receiving pickups
        $generalBlock = GeneralBlock::all()->first();

        $generalBlock->stopPickup = ! boolval($generalBlock->stopPickup);
        $generalBlock->save();


        return response()->json(['status' => true, 'message' => 'Store receiving has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------



    public function store(Request $request)
    {


        // 1: create item
        $pickup = new PickupStore();

        $pickup->serial = $this->createSerial('PS', PickupStore::latest()->first() ? PickupStore::latest()->first()->id : 0);
        $pickup->title = $request->title;
        $pickup->titleAr = $request->titleAr;

        $pickup->desc = $request->desc;
        $pickup->descAr = $request->descAr;
        $pickup->receivingTimes = $request->receivingTimes;
        $pickup->receivingTimesAr = $request->receivingTimesAr;


        $pickup->latitude = $request->latitude;
        $pickup->longitude = $request->longitude;

        $pickup->isMainStore = $request->isMainStore == 'true' ? true : false;
        $pickup->isActive = $request->isActive == 'true' ? false : true;

        // 1.2: upload image if exits
        if ($request->hasFile('image')) {

            $fileName = $this->uploadFile($request, 'image', 'pickups/');
            $pickup->image = $fileName;

        } // end if


        $pickup->save();



        return response()->json(['status' => true, 'message' => 'Store has been added!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function edit(Request $request, $id)
    {


        // 1: get pickup
        $pickup = PickupStore::find($id);

        return response()->json($pickup, 200);

    } // end function



    // ----------------------------------------------------------



    public function update(Request $request, $id)
    {

        // :: validator
        $validator = $this->validationTrait($request,
            ['title' => 'required', 'titleAr' => 'required', 'desc' => 'required', 'descAr' => 'required', 'latitude' => 'required', 'longitude' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------

        // 1: update item
        $pickup = PickupStore::find($id);

        $pickup->title = $request->title;
        $pickup->titleAr = $request->titleAr;

        $pickup->desc = $request->desc;
        $pickup->descAr = $request->descAr;
        $pickup->receivingTimes = $request->receivingTimes;
        $pickup->receivingTimesAr = $request->receivingTimesAr;

        $pickup->latitude = $request->latitude;
        $pickup->longitude = $request->longitude;

        $pickup->isMainStore = $request->isMainStore == 'true' ? true : false;
        $pickup->isActive = $request->isActive == 'true' ? false : true;



        // 1.2: upload image if exits / remove
        if ($request->hasFile('image')) {

            $this->deleteFile($pickup->image, 'pickups/');
            $fileName = $this->uploadFile($request, 'image', 'pickups/');
            $pickup->image = $fileName;

        } // end if

        $pickup->save();



        return response()->json(['status' => true, 'message' => 'Store has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------




    public function toggleActive(Request $request, $id)
    {


        // 1: get pickup
        $pickup = PickupStore::find($id);

        $pickup->isActive = ! boolval($pickup->isActive);
        $pickup->save();


        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------



    public function delete(Request $request, $id)
    {

        // 1: delete item / image
        $pickup = PickupStore::find($id);
        $pickup->delete();

        return response()->json(['status' => true, 'message' => 'Store has been removed!'], 200);

    } // end function











    // ===============================================================================
    // ===============================================================================
    // ===============================================================================
    // ===============================================================================
    // ===============================================================================









    public function conditions()
    {

        // ::get items
        $conditions = PickupCondition::all();

        return response()->json($conditions, 200);

    } // end function


    // ----------------------------------------------------------



    public function storeCondition(Request $request)
    {

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
        $condition = new PickupCondition();

        $condition->serial = $this->createSerial('PC', PickupCondition::latest()->first() ? PickupCondition::latest()->first()->id : 0);
        $condition->title = $request->title;
        $condition->titleAr = $request->titleAr;

        $condition->content = $request->content;
        $condition->contentAr = $request->contentAr;

        $condition->save();

        return response()->json(['status' => true, 'message' => 'Condition has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updateCondition(Request $request)
    {


        // 1: create item
        $condition = PickupCondition::find($request->id);

        $condition->title = $request->title;
        $condition->titleAr = $request->titleAr;

        $condition->content = $request->content;
        $condition->contentAr = $request->contentAr;

        $condition->save();

        return response()->json(['status' => true, 'message' => 'Condition has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function deleteCondition(Request $request, $id)
    {

        // 1: delete item / image
        $condition = PickupCondition::find($id);
        $condition->delete();

        return response()->json(['status' => true, 'message' => 'Condition has been removed!'], 200);

    } // end function





} // end controller
