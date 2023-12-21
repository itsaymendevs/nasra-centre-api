<?php

namespace App\Http\Controllers;

use App\Models\AboutInfo;
use App\Models\AddressInfo;
use App\Models\MediaInfo;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;

class HelpController extends Controller {
    
    // :: use trait
    use AppTrait;




    public function index() {

        // ::get items
        $media = MediaInfo::all()->first();
        $aboutParagraphs = AboutInfo::all();
        $address = AddressInfo::all()->first();

        // 1: combine into one object
        $combine = new stdClass();
        $combine->media = $media;
        $combine->aboutParagraphs = $aboutParagraphs;
        $combine->address = $address;

        return response()->json($combine, 200);

    } // end function






    // ----------------------------------------------------------




    public function updateMedia(Request $request) {

        // 1: get media
        $media = MediaInfo::all()->first();


        // 2: update
        $media->websiteURL = $request->websiteURL;
        $media->facebookID = $request->facebookID;
        $media->facebookURL = $request->facebookURL;
        $media->linkedinID = $request->linkedinID;
        $media->linkedinURL = $request->linkedinURL;
        $media->twitterID = $request->twitterID;
        $media->twitterURL = $request->twitterURL;
        $media->instagramID = $request->instagramID;
        $media->instagramURL = $request->instagramURL;
        $media->videoTitle = $request->videoTitle;
        $media->videoTitleAr = $request->videoTitleAr;
        $media->videoURL = $request->videoURL;

        $media->save();        
        
        return response()->json(['status' => true, 'message' => 'Media has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------




    public function updateAddress(Request $request) {

        // 1: update address
        $address = AddressInfo::all()->first();

        $address->address = $request->address;
        $address->longitude = $request->longitude;
        $address->latitude = $request->latitude;
        $address->isHidden = $request->isHidden == 'true' ? true : false;


        if ($request->hasFile('image')) {
            
            $this->deleteFile($address->image, 'interAddress/');
            $fileName = $this->uploadFile($request, 'image', 'interAddress/');
            $address->image = $fileName;

        } // end if


        $address->save();


        return response()->json(['status' => true, 'message' => 'Address has been updated!'], 200);

    } // end function








    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // ----------------------------------------------------------





    public function storeAbout(Request $request) {

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
        $paragraph = new AboutInfo();

        $paragraph->serial = $this->createSerial('AP', AboutInfo::count());
        $paragraph->title = $request->title;
        $paragraph->titleAr = $request->titleAr;

        $paragraph->content = $request->content;
        $paragraph->contentAr = $request->contentAr;

        $paragraph->save();

        return response()->json(['status' => true, 'message' => 'Info has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updateAbout(Request $request) {

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
        $paragraph = AboutInfo::find($request->id);

        $paragraph->title = $request->title;
        $paragraph->titleAr = $request->titleAr;

        $paragraph->content = $request->content;
        $paragraph->contentAr = $request->contentAr;

        $paragraph->save();

        return response()->json(['status' => true, 'message' => 'Info has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function deleteAbout(Request $request, $id) {

        // 1: delete item / image
        $paragraph = AboutInfo::find($id);
        $paragraph->delete();

        return response()->json(['status' => true, 'message' => 'Info has been removed!'], 200);

    } // end function






} // end controller
