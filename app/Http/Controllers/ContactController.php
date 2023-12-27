<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactPhone;
use App\Models\Country;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Traits\AppTrait;
use stdClass;

class ContactController extends Controller {

    // :: use trait
    use AppTrait;

    

    public function index(Request $request, $countryId) {

        // ::get items
        $contact = Contact::where('countryId', $countryId)->first();
        $phones = ContactPhone::where('countryId', $countryId)->get();
        $terms = Term::where('countryId', $countryId)->get();
        $country = Country::find($countryId);

        
        // 1: combine into one object
        $combine = new stdClass();
        $combine->contact = $contact;
        $combine->phones = $phones;
        $combine->terms = $terms;
        $combine->country = $country;

        return response()->json($combine, 200);

    } // end function






    // ----------------------------------------------------------



    public function update(Request $request, $countryId) {

        // 1: update item
        $contact = Contact::where('countryId', $countryId)->first();

        $contact->email = $request->email;
        $contact->whatsapp = $request->whatsapp;
        $contact->whatsappURL = $request->whatsappURL;

        $contact->save();

        return response()->json(['status' => true, 'message' => 'Info has been updated!'], 200);

    } // end function







    // ----------------------------------------------------------



    public function updateService(Request $request, $countryId) {


        // 1: update country
        $contact = Country::find($countryId);

        $contact->isServiceActive = $request->isServiceActive == 'true' ? true : false;
        $contact->toSDG = $request->toSDG > 0 ? $request->toSDG : 0;

        $contact->save();

        return response()->json(['status' => true, 'message' => 'Info has been updated!'], 200);

    } // end function





    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // ----------------------------------------------------------





    public function storePhone(Request $request, $countryId) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['phone' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $phone = new ContactPhone();

        $phone->serial = $this->createSerial('PH', ContactPhone::where('countryId', $countryId)->latest()->first() ? ContactPhone::where('countryId', $countryId)->latest()->first()->id : 0);

        $phone->phone = $request->phone;
        $phone->countryId = $countryId;
        
        $phone->save();

        return response()->json(['status' => true, 'message' => 'Phone has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updatePhone(Request $request, $countryId) {

        // :: validator
        $validator = $this->validationTrait($request, 
        ['phone' => 'required']);

        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: create item
        $phone = ContactPhone::find($request->id);

        $phone->phone = $request->phone;

        $phone->save();

        return response()->json(['status' => true, 'message' => 'Phone has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function deletePhone(Request $request, $countryId, $id) {

        // 1: delete item / image
        $phone = ContactPhone::find($id);
        $phone->delete();

        return response()->json(['status' => true, 'message' => 'Phone has been removed!'], 200);

    } // end function










    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // ----------------------------------------------------------





    public function storeTerm(Request $request, $countryId) {

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
        $term = new Term();

        $term->serial = $this->createSerial('TR', Term::where('countryId', $countryId)->latest()->first() ? Term::where('countryId', $countryId)->latest()->first()->id : 0);
        $term->title = $request->title;
        $term->titleAr = $request->titleAr;

        $term->content = $request->content;
        $term->contentAr = $request->contentAr;

        $term->countryId = $countryId;

        $term->save();

        return response()->json(['status' => true, 'message' => 'Term has been added!'], 200);

    } // end function








    // ----------------------------------------------------------



    public function updateTerm(Request $request, $countryId) {

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
        $term = Term::find($request->id);

        $term->title = $request->title;
        $term->titleAr = $request->titleAr;

        $term->content = $request->content;
        $term->contentAr = $request->contentAr;


        $term->save();

        return response()->json(['status' => true, 'message' => 'Term has been updated!'], 200);

    } // end function






    // ----------------------------------------------------------



    public function deleteTerm(Request $request, $countryId, $id) {

        // 1: delete item / image
        $term = Term::find($id);
        $term->delete();

        return response()->json(['status' => true, 'message' => 'Term has been removed!'], 200);

    } // end function





} // end controller
