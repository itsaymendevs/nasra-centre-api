<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\AboutInfo;
use App\Models\AddressInfo;
use App\Models\Contact;
use App\Models\ContactPhone;
use App\Models\Country;
use App\Models\DeliveryCondition;
use App\Models\GeneralBlock;
use App\Models\MediaInfo;
use App\Models\PickupCondition;
use App\Models\PickupStore;
use App\Models\Term;
use Illuminate\Http\Request;
use stdClass;

ini_set('max_execution_time', 180); // 180 (seconds) = 3 Minutes




class InfoController extends Controller {
    




    public function helpInfo(Request $request) {

        // 1: get data (help / contact)
        $response = new stdClass();
        $response->helpAndContactInfo = new stdClass();
        $response->helpAndContactInfo->contactInfo = new stdClass();


        // 1.1: contact
        $contact = Contact::where('countryId', 1)->first(); // SDN

        $response->helpAndContactInfo->contactInfo->emailAddress = $contact->email;
        $response->helpAndContactInfo->contactInfo->whatsappNum = intval($contact->whatsapp);
        $response->helpAndContactInfo->contactInfo->whatsappURL = $contact->whatsappURL;


        // 1.2: contact numbers

        $phones = ContactPhone::where('countryId', 1)->get(); // SDN

        $contentArray = array();
        foreach ($phones as $phone) {

            array_push($contentArray, intval($phone->phone));

        } // end loop

        // ::prepare response
        $response->helpAndContactInfo->contactInfo->contactNumbers = $contentArray;








        // 1.3: terms
        $terms = Term::where('countryId', 1)->get(); // SDN

        $contentArray = array();
        foreach ($terms as $term) {

            $content = new stdClass();
            $content->id = $term->id;
            $content->title = $term->title;
            $content->titleAr = $term->titleAr;

            $content->content = $term->content;
            $content->contentAr = $term->contentAr;

            array_push($contentArray, $content);

        } // end loop

        // ::prepare response
        $response->helpAndContactInfo->contactInfo->appTermsAndConditions = $contentArray;








        // -----------------------------------------------------------------
        // -----------------------------------------------------------------
        // -----------------------------------------------------------------


        // 2: help info
        $response->helpAndContactInfo->helpInfo = new stdClass();


        // 2.1: help - media
        $media = MediaInfo::all()->first();

        $response->helpAndContactInfo->helpInfo->facebookURL = $media->facebookURL;
        $response->helpAndContactInfo->helpInfo->facebookID = $media->facebookID;
        $response->helpAndContactInfo->helpInfo->linkedinURL = $media->linkedinURL;
        $response->helpAndContactInfo->helpInfo->linkedinID = $media->linkedinID;
        $response->helpAndContactInfo->helpInfo->instagramURL = $media->instagramURL;
        $response->helpAndContactInfo->helpInfo->instagramID = $media->instagramID;
        $response->helpAndContactInfo->helpInfo->twitterURL = $media->twitterURL;
        $response->helpAndContactInfo->helpInfo->twitterID = $media->twitterID;
        $response->helpAndContactInfo->helpInfo->videoTitle = $media->videoTitle;
        $response->helpAndContactInfo->helpInfo->videoTitleAr = $media->videoTitleAr;
        $response->helpAndContactInfo->helpInfo->videoURL = $media->videoURL;
        $response->helpAndContactInfo->helpInfo->websiteURL = $media->websiteURL;

        


        // 2.3: interAddress
        $interAddress = AddressInfo::all()->first();

        if (boolval($interAddress->isHidden) === true) {

            $response->helpAndContactInfo->helpInfo->interAddress = null;

        } else {

            $response->helpAndContactInfo->helpInfo->interAddress = new stdClass();
            $response->helpAndContactInfo->helpInfo->interAddress->address = $interAddress->address;
            $response->helpAndContactInfo->helpInfo->interAddress->addressImage = $interAddress->image;
            $response->helpAndContactInfo->helpInfo->interAddress->latitude = $interAddress->latitude;
            $response->helpAndContactInfo->helpInfo->interAddress->longitude = $interAddress->longitude;


        } // end if

        





        // 2.4: about nasra
        $aboutInfos = AboutInfo::all();

        $response->helpAndContactInfo->helpInfo->aboutNasra = new stdClass();


        $contentArray = array();
        foreach ($aboutInfos as $aboutInfo) {

            $content = new stdClass();
            $content->id = $aboutInfo->id;
            $content->title = $aboutInfo->title;
            $content->titleAr = $aboutInfo->titleAr;

            $content->content = $aboutInfo->content;
            $content->contentAr = $aboutInfo->contentAr;

            array_push($contentArray, $content);

        } // end loop

        // ::prepare response
        $response->helpAndContactInfo->helpInfo->aboutNasra = $contentArray;



        return response()->json($response, 200);

    } // end function






    // -----------------------------------------------------------------











    public function pickupDeliveryInfo(Request $request) {

        // 1: get data (payment / pickup / delivery)
        $response = new stdClass();
        $response->PickupAndDeliveryAndPaymentInfo = new stdClass();


        // 1.1: Payment Types + Payment Conditions
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo = new stdClass();

        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->directPayment = [];
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->onlineBankingPayments = [];
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->atReceivingPayments = [];
        $response->PickupAndDeliveryAndPaymentInfo->paymentInfo->termsAndConditions = [];




        // -----------------------------------------------------------------
        // -----------------------------------------------------------------
        // -----------------------------------------------------------------






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

            $content->isPickupAtStoreBlocked = !boolval($store->isActive);

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







        // 1.2.2: isDeliveryBlocked
        $stopDelivery = GeneralBlock::all()->first()->stopDelivery;

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->deliveryInfo->isDeliveryBlocked = boolval($stopDelivery);






        // -----------------------------------------------------------------
        // -----------------------------------------------------------------
        // -----------------------------------------------------------------






        // 1.3: isOrderingBlocked
        $stopOrders = GeneralBlock::all()->first()->stopOrders;

        // ::prepare response
        $response->PickupAndDeliveryAndPaymentInfo->isOrderingBlocked = boolval($stopOrders);





        // 1.4: isSDNOrderingBlocked / isUKOrderingBlocked / isIRLOrderingBlocked
        $country = Country::find(1);
        $response->isSDNOrderingBlocked = !boolval($country->isOrderingActive);

        $country = Country::find(2);
        $response->isUKOrderingBlocked = !boolval($country->isOrderingActive);

        $country = Country::find(3);
        $response->isIRLOrderingBlocked = !boolval($country->isOrderingActive);



        


        return response()->json($response, 200);
        

    } // end function

    



} // end controller
