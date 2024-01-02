<?php

use App\Http\Controllers\Api\InfoController;
use App\Http\Controllers\Api\InterUserController;
use App\Http\Controllers\Api\LaunchController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PreviousOrderController;
use App\Http\Controllers\Api\ProductController as ProductControllerApp;
use App\Http\Controllers\Api\UserController as UserControllerApp;
use App\Http\Controllers\Api\UserEditController;
use App\Http\Controllers\Api\InterUserEditController;


use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\MainCategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController as LocalOrderController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------

Route::group(['middleware' => 'cors'], function () {

    // 1: main-category
    Route::post("/login", [EmployeeController::class, 'login']);




    // ========================================================
    // ========================================================



    // 1: main-category
    Route::get("/main-categories", [MainCategoryController::class, 'index']);


    // sort - updateSort
    Route::get("/main-categories/sort", [MainCategoryController::class, 'sort']);




    // ========================================================
    // ========================================================


    // 2: sub-category
    Route::get("/sub-categories", [SubCategoryController::class, 'index']);

    // sort - updateSort
    Route::get("/sub-categories/{mainCategoryId}/sort", [SubCategoryController::class, 'sort']);




    // ========================================================
    // ========================================================


    // 3: inner-types
    Route::get("/inner-types", [TypeController::class, 'index']);


    // sort - updateSort
    Route::get("/inner-types/{mainCategoryId}/{subCategoryId}/sort", [TypeController::class, 'sort']);




    // ========================================================
    // ========================================================


    // 4: companies
    Route::get("/companies", [CompanyController::class, 'index']);





    // ========================================================
    // ========================================================


    // 5: units
    Route::get("/units", [UnitController::class, 'index']);





    // ========================================================
    // ========================================================


    // 6: employees
    Route::get("/employees", [EmployeeController::class, 'index']);




    // ========================================================
    // ========================================================


    // 7: Help
    Route::get("/help", [HelpController::class, 'index']);



    // ========================================================
    // ========================================================


    // 8: contact
    Route::get("/contact/{countryId}", [ContactController::class, 'index']);




    // ========================================================
    // ========================================================


    // 9: pickup
    Route::get("/pickup", [PickupController::class, 'index']);

    // store - update
    Route::get("/pickup/create", [PickupController::class, 'create']);

    Route::get("/pickup/{id}/edit", [PickupController::class, 'edit']);




    // 9.1: pickup conditions
    Route::get("/pickup/conditions", [PickupController::class, 'conditions']);







    // ========================================================
    // ========================================================


    // 10: delivery
    Route::get("/delivery", [DeliveryController::class, 'index']);

    // store - update
    Route::get("/delivery/create", [DeliveryController::class, 'create']);

    Route::get("/delivery/{id}/edit", [DeliveryController::class, 'edit']);





    // 10.1: delivery conditions
    Route::get("/delivery/conditions", [DeliveryController::class, 'conditions']);




    // 10.2: delivery times
    Route::get("/delivery/times", [DeliveryController::class, 'times']);




    // ========================================================
    // ========================================================


    // 11.1: messages - local
    Route::get("/messages", [MessageController::class, 'index']);


    // 11.2: messages - global
    Route::get("/messages-global", [MessageController::class, 'indexGlobal']);







    // ========================================================
    // ========================================================


    // 12: products
    Route::get("/products", [ProductController::class, 'index']);

    // global edits - prices / quantity
    Route::get("/products/shorthand", [ProductController::class, 'shorthand']);


    // create - store
    Route::get("/products/create", [ProductController::class, 'create']);

    // edit - update
    Route::get("/products/{id}/edit", [ProductController::class, 'edit']);


    // 12.1: sort - updateSort (based on typeId)
    Route::get("/products/main-page/sort", [ProductController::class, 'mainPageSort']);

    Route::get("/products/{type}/sort", [ProductController::class, 'typeSort']);






    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------






    // 13: users
    Route::get("/users", [UserController::class, 'index']);



    // 13.1: single User
    Route::get("/users/{id}", [UserController::class, 'singleUser']);


    // 13.2: single User - Order
    Route::get("/users/{id}/orders/{orderId}", [UserController::class, 'singleUserOrder']);



    // 13.3: single Receiver
    Route::get("/users/{id}/receivers/{receiverId}", [UserController::class, 'singleReceiver']);






    // ========================================================
    // ========================================================


    // 14: payments
    Route::get("/payments", [PaymentController::class, 'index']);

    Route::get("/payments/conditions", [PaymentController::class, 'conditions']);



    // ========================================================
    // ========================================================


    // 15: orders
    Route::get("/previousOrders", [LocalOrderController::class, 'previousOrders']);
    Route::get("/orders", [LocalOrderController::class, 'index']);




    // ========================================================
    // ========================================================


    // 16: SingleOrder
    Route::get("/orders/{id}", [LocalOrderController::class, 'singleOrder']);









    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------





    // 1: employee middleware
    Route::middleware(['auth:sanctum', 'auth.employee'])->group(function() {

        // categories - covers
        Route::post("/categories/update", [MainCategoryController::class, 'updateCovers']);






        // store - update - remove
        Route::post("/main-categories/store", [MainCategoryController::class, 'store']);
        Route::post("/main-categories/update", [MainCategoryController::class, 'update']);
        Route::delete("/main-categories/{id}/delete", [MainCategoryController::class, 'delete']);

        // sort - updateSort
        Route::patch("/main-categories/sort/update", [MainCategoryController::class, 'updateSort']);




        // ========================================================
        // ========================================================


        // 2: sub-category

        // store - update
        Route::post("/sub-categories/store", [SubCategoryController::class, 'store']);
        Route::patch("/sub-categories/update", [SubCategoryController::class, 'update']);
        Route::delete("/sub-categories/{id}/delete", [SubCategoryController::class, 'delete']);

        // sort - updateSort
        Route::patch("/sub-categories/{mainCategoryId}/sort/update", [SubCategoryController::class, 'updateSort']);




        // ========================================================
        // ========================================================


        // 3: inner-types

        // store - update
        Route::post("/inner-types/store", [TypeController::class, 'store']);
        Route::patch("/inner-types/update", [TypeController::class, 'update']);
        Route::delete("/inner-types/{id}/delete", [TypeController::class, 'delete']);
        // sort - updateSort
        Route::patch("/inner-types/{mainCategoryId}/{subCategoryId}/sort/update", [TypeController::class, 'updateSort']);





        // ========================================================
        // ========================================================


        // 4: companies

        // store - update
        Route::post("/companies/store", [CompanyController::class, 'store']);
        Route::patch("/companies/update", [CompanyController::class, 'update']);
        Route::delete("/companies/{id}/delete", [CompanyController::class, 'delete']);





        // ========================================================
        // ========================================================


        // 5: units

        // store - update
        Route::post("/units/store", [UnitController::class, 'store']);
        Route::patch("/units/update", [UnitController::class, 'update']);
        Route::delete("/units/{id}/delete", [UnitController::class, 'delete']);





        // ========================================================
        // ========================================================


        // 6: employees

        // store - update
        Route::post("/employees/store", [EmployeeController::class, 'store']);
        Route::patch("/employees/update", [EmployeeController::class, 'update']);
        Route::patch("/employees/reset-password", [EmployeeController::class, 'resetPassword']);


        Route::delete("/employees/{id}/delete", [EmployeeController::class, 'delete']);

        // toggle active
        Route::patch("/employees/{id}/toggle-active", [EmployeeController::class, 'toggleActive']);






        // ========================================================
        // ========================================================


        // 7: Help

        // 7.1: update media - address
        Route::patch("/help/media/update", [HelpController::class, 'updateMedia']);
        Route::post("/help/address/update", [HelpController::class, 'updateAddress']);


        // 7.1: store - update about paragraphs
        Route::post("/help/about/store", [HelpController::class, 'storeAbout']);
        Route::patch("/help/about/update", [HelpController::class, 'updateAbout']);

        Route::delete("/help/about/{id}/delete", [HelpController::class, 'deleteAbout']);






        // ========================================================
        // ========================================================


        // 8: contact

        // 8.1: update
        Route::patch("/contact/{countryId}/update", [ContactController::class, 'update']);

        Route::patch("/contact/{countryId}/update-service", [ContactController::class, 'updateService']);



        // 8.2: contact phone => store - update - delete
        Route::post("/contact/{countryId}/phones/store", [ContactController::class, 'storePhone']);

        Route::patch("/contact/{countryId}/phones/update", [ContactController::class, 'updatePhone']);

        Route::delete("/contact/{countryId}/phones/{id}/delete", [ContactController::class, 'deletePhone']);



        // 8.3: contact terms & conditions => store - update
        Route::post("/contact/{countryId}/terms/store", [ContactController::class, 'storeTerm']);

        Route::patch("/contact/{countryId}/terms/update", [ContactController::class, 'updateTerm']);

        Route::delete("/contact/{countryId}/terms/{id}/delete", [ContactController::class, 'deleteTerm']);







        // ========================================================
        // ========================================================


        // 9: pickup

        // ::special - stop receiving in all stores
        Route::patch("/pickup/toggle-receiving", [PickupController::class, 'toggleReceiving']);


        // store - update
        Route::post("/pickup/store", [PickupController::class, 'store']);

        Route::post("/pickup/{id}/update", [PickupController::class, 'update']);
        Route::delete("/pickup/{id}/delete", [PickupController::class, 'delete']);

        // toggle active
        Route::patch("/pickup/{id}/toggle-active", [PickupController::class, 'toggleActive']);




        // 9.1: pickup conditions

        Route::post("/pickup/conditions/store", [PickupController::class, 'storeCondition']);

        Route::patch("/pickup/conditions/update", [PickupController::class, 'updateCondition']);

        Route::delete("/pickup/conditions/{id}/delete", [PickupController::class, 'deleteCondition']);











        // ========================================================
        // ========================================================


        // 10: delivery

        // ::special - stop delivery in all areas
        Route::patch("/delivery/toggle-delivery", [DeliveryController::class, 'toggleDelivery']);


        // store - update
        Route::post("/delivery/store", [DeliveryController::class, 'store']);

        Route::patch("/delivery/{id}/update", [DeliveryController::class, 'update']);
        Route::delete("/delivery/{id}/delete", [DeliveryController::class, 'delete']);

        // toggle active
        Route::patch("/delivery/{id}/toggle-active", [DeliveryController::class, 'toggleActive']);




        // 10.1: delivery conditions

        Route::post("/delivery/conditions/store", [DeliveryController::class, 'storeCondition']);

        Route::post("/delivery/conditions/update", [DeliveryController::class, 'updateCondition']);
        Route::delete("/delivery/conditions/{id}/delete", [DeliveryController::class, 'deleteCondition']);





        // 10.2: delivery times
        Route::post("/delivery/times/store", [DeliveryController::class, 'storeTime']);

        Route::post("/delivery/times/update", [DeliveryController::class, 'updateTime']);
        Route::delete("/delivery/times/{id}/delete", [DeliveryController::class, 'deleteTime']);






        // ========================================================
        // ========================================================


        // 11.1: messages - local


        // update - toggle
        Route::patch("/messages/update", [MessageController::class, 'update']);
        Route::patch("/messages/toggle-active", [MessageController::class, 'toggleActive']);



        // 11.2: messages - global


        // update - toggle
        Route::patch("/messages-global/update", [MessageController::class, 'updateGlobal']);

        Route::patch("/messages-global/toggle-active", [MessageController::class, 'toggleActiveGlobal']);







        // ========================================================
        // ========================================================


        // 12: products

        // global edits - prices / quantity
        Route::patch("/products/shorthand/update", [ProductController::class, 'updateShorthand']);


        // create - store
        Route::post("/products/store", [ProductController::class, 'store']);

        // update - delete
        Route::post("/products/{id}/update", [ProductController::class, 'update']);
        Route::delete("/products/{id}/delete", [ProductController::class, 'delete']);



        // toggle show / home
        Route::patch("/products/{id}/toggle-home", [ProductController::class, 'toggleHome']);

        Route::patch("/products/{id}/toggle-hidden", [ProductController::class, 'toggleHidden']);



        // 12.1: sort - updateSort (based on type)
        Route::patch("/products/main-page/sort/update", [ProductController::class, 'updateMainPageSort']);


        Route::patch("/products/{type}/sort/update", [ProductController::class, 'updateTypeSort']);






        // ========================================================
        // ========================================================



        // 13: users


        // 13.1: toggle active / inactive
        Route::patch("/users/{id}/toggle-active", [UserController::class, 'toggleActive']);



        Route::patch("/users/{id}/toggle-active", [UserController::class, 'toggleActive']);





        // ========================================================
        // ========================================================


        // 14: payments

        // store - update
        Route::post("/payments/store", [PaymentController::class, 'store']);
        Route::patch("/payments/update", [PaymentController::class, 'update']);

        // toggle active
        Route::patch("/payments/{id}/toggle-active", [PaymentController::class, 'toggleActive']);

        Route::delete("/payments/{id}/delete", [PaymentController::class, 'delete']);





        // 14.2: payment conditions

        Route::post("/payments/conditions/store", [PaymentController::class, 'storeCondition']);

        Route::post("/payments/conditions/update", [PaymentController::class, 'updateCondition']);
        Route::delete("/payments/conditions/{id}/delete", [PaymentController::class, 'deleteCondition']);




        // ========================================================
        // ========================================================




        // 15: Orders / PreviousOrders
        Route::patch("/previousOrders/toggle-ordering", [LocalOrderController::class, 'toggleOrdering']);
        Route::patch("/previousOrders/toggle-global-ordering", [LocalOrderController::class, 'toggleGlobalOrdering']);







        // ========================================================
        // ========================================================



        // 16.1: Payment
        Route::patch("/orders/{id}/confirm-payment", [LocalOrderController::class, 'confirmPayment']);
        Route::patch("/orders/{id}/cancel-payment", [LocalOrderController::class, 'cancelPayment']);




        // 16.2: OTP
        Route::patch("/orders/{id}/send-otp", [LocalOrderController::class, 'sendOTP']);


        // 16.3: OrderNote
        Route::patch("/orders/{id}/update-note", [LocalOrderController::class, 'updateOrderNote']);



        // 16.4: processOrder / cancelOrder
        Route::patch("/orders/{id}/process-order", [LocalOrderController::class, 'processOrder']);
        Route::patch("/orders/{id}/cancel-order", [LocalOrderController::class, 'cancelOrder']);




    }); // end sanctum middleware - Employee












    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------
    // ----------------------------------------------------------------------------






    // 1: Launch
    Route::post("/app/launch", [LaunchController::class, 'launch']);


    // 1.2: Launch - subcategory products
    Route::post("/app/launch/subCategoryProducts", [LaunchController::class, 'subCategoryProducts']);

    // 1.3: Launch - offer products
    Route::post("/app/launch/offerProducts", [LaunchController::class, 'offerProducts']);




    // ========================================================
    // ========================================================




    // 2: Launch - help
    Route::post("/app/launch/helpInfo", [InfoController::class, 'helpInfo']);


    // 2.2: Launch - pdp
    Route::post("/app/launch/pdp", [InfoController::class, 'pickupDeliveryInfo']);






    // ========================================================
    // ========================================================




    // 3: Search Products
    Route::post("/app/launch/searchProducts", [ProductControllerApp::class, 'searchProducts']);





    // ========================================================
    // ========================================================



    // 3.5: Auth - Search Products (auth)
    Route::post("/app/launch/searchProductsAuth", [ProductControllerApp::class, 'searchProductsAuth']);







    // ========================================================
    // ========================================================


    // 4: login
    Route::post("/app/user/login", [UserControllerApp::class, 'login']);



    // 4.1: register
    Route::post("/app/user/register", [UserControllerApp::class, 'register']);
    Route::post("/app/user/register/confirm", [UserControllerApp::class, 'confirmRegister']);
    Route::post("/app/user/register/resend", [UserControllerApp::class, 'registerResend']);




    // 4.2: reset-password
    Route::post("/app/user/resetPassword/getOTP", [UserEditController::class, 'resetPasswordOTP']);
    Route::post("/app/user/resetPassword/resendOTP", [UserEditController::class, 'resendResetPasswordOTP']);
    Route::post("/app/user/resetPassword/confirmOTP",  [UserEditController::class, 'confirmResetPasswordOTP']);
    Route::post("/app/user/resetPassword", [UserEditController::class, 'resetPassword']);






    // 4.1: reset-phone (auth)
    Route::post("/app/user/changeNumber/getOTP", [UserEditController::class, 'changeNumberOTP']);
    Route::post("/app/user/changeNumber/resendOTP", [UserEditController::class, 'resendChangeNumberOTP']);
    Route::post("/app/user/changeNumber/confirmOTP", [UserEditController::class, 'confirmChangeNumberOTP']);



    // 4.2: Change Email / Password / Address (auth)
    Route::post("/app/user/changeEmail", [UserEditController::class, 'changeEmail']);
    Route::post("/app/user/changePassword", [UserEditController::class, 'changePassword']);
    Route::post("/app/user/changeAddress", [UserEditController::class, 'changeAddress']);




    // 4.3: logout (auth)
    Route::post("/app/user/logout", [UserControllerApp::class, 'logout']);



    // ========================================================
    // ========================================================





    // 5: login
    Route::post("/app/user/loginInter", [InterUserController::class, 'login']);



    // 5.1: register
    Route::post("/app/user/registerInter", [InterUserController::class, 'register']);
    Route::post("/app/user/registerInter/confirm", [InterUserController::class, 'confirmRegister']);
    Route::post("/app/user/registerInter/resend", [InterUserController::class, 'registerResend']);




    // 5.2: reset-password
    Route::post("/app/user/resetInterPassword/getOTP", [InterUserEditController::class, 'resetPasswordOTP']);
    Route::post("/app/user/resetInterPassword/resendOTP", [InterUserEditController::class, 'resendResetPasswordOTP']);
    Route::post("/app/user/resetInterPassword/confirmOTP",  [InterUserEditController::class, 'confirmResetPasswordOTP']);
    Route::post("/app/user/resetInterPassword", [InterUserEditController::class, 'resetPassword']);






    // 5.1: reset-phone (auth)
    Route::post("/app/user/changeInterNumber/getOTP", [InterUserEditController::class, 'changeNumberOTP']);
    Route::post("/app/user/changeInterNumber/resendOTP", [InterUserEditController::class, 'resendChangeNumberOTP']);
    Route::post("/app/user/changeInterNumber/confirmOTP", [InterUserEditController::class, 'confirmChangeNumberOTP']);



    // 5.2: Change Email / Password / Address (auth)
    Route::post("/app/user/changeInterEmail", [InterUserEditController::class, 'changeEmail']);
    Route::post("/app/user/changeInterPassword", [InterUserEditController::class, 'changePassword']);
    Route::post("/app/user/changeInterAddress", [InterUserEditController::class, 'changeAddress']);







    // ========================================================
    // ========================================================







    // 6: Add / update / remove receiver (auth)
    Route::post("/app/user/addReceiver", [InterUserEditController::class, 'storeReceiver']);
    Route::post("/app/user/updateReceiver", [InterUserEditController::class, 'updateReceiver']);
    Route::post("/app/user/removeReceiver", [InterUserEditController::class, 'removeReceiver']);













    // ========================================================
    // ========================================================


    // 7: makeOrder / Previous Orders (auth)
    Route::post("/app/user/makeOrder", [OrderController::class, 'makeOrder']);
    Route::post("/app/user/previousOrders", [PreviousOrderController::class, 'previousOrders']);




}); // end cors middleware


