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
use App\Http\Controllers\PickupController;
use App\Http\Controllers\ProductController;
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
    Route::get("/main-categories", [MainCategoryController::class, 'index'])->name('mainCategory.index');

  
    // sort - updateSort
    Route::get("/main-categories/sort", [MainCategoryController::class, 'sort'])->name('mainCategory.sort');




    // ========================================================
    // ========================================================


    // 2: sub-category
    Route::get("/sub-categories", [SubCategoryController::class, 'index'])->name('subCategory.index');

    // sort - updateSort
    Route::get("/sub-categories/{mainCategoryId}/sort", [SubCategoryController::class, 'sort'])->name('subCategory.sort');
 



    // ========================================================
    // ========================================================


    // 3: inner-types
    Route::get("/inner-types", [TypeController::class, 'index'])->name('innerType.index');

   
    // sort - updateSort
    Route::get("/inner-types/{mainCategoryId}/{subCategoryId}/sort", [TypeController::class, 'sort'])->name('innerType.sort');
  



    // ========================================================
    // ========================================================


    // 4: companies
    Route::get("/companies", [CompanyController::class, 'index'])->name('company.index');





    // ========================================================
    // ========================================================


    // 5: units
    Route::get("/units", [UnitController::class, 'index'])->name('unit.index');





    // ========================================================
    // ========================================================


    // 6: employees
    Route::get("/employees", [EmployeeController::class, 'index'])->name('employee.index');




    // ========================================================
    // ========================================================


    // 7: Help
    Route::get("/help", [HelpController::class, 'index'])->name('help.index');



    // ========================================================
    // ========================================================


    // 8: contact
    Route::get("/contact/{countryId}", [ContactController::class, 'index'])->name('contact.index');




    // ========================================================
    // ========================================================


    // 9: pickup
    Route::get("/pickup", [PickupController::class, 'index'])->name('pickup.index');

    // store - update
    Route::get("/pickup/create", [PickupController::class, 'create'])->name('pickup.create');

    Route::get("/pickup/{id}/edit", [PickupController::class, 'edit'])->name('pickup.edit');
 



    // 9.1: pickup conditions
    Route::get("/pickup/conditions", [PickupController::class, 'conditions'])->name('pickup.conditions');







    // ========================================================
    // ========================================================


    // 10: delivery
    Route::get("/delivery", [DeliveryController::class, 'index'])->name('delivery.index');

    // store - update
    Route::get("/delivery/create", [DeliveryController::class, 'create'])->name('delivery.create');

    Route::get("/delivery/{id}/edit", [DeliveryController::class, 'edit'])->name('delivery.edit');
   



    // 10.1: delivery conditions
    Route::get("/delivery/conditions", [DeliveryController::class, 'conditions'])->name('delivery.conditions');






    // ========================================================
    // ========================================================


    // 11.1: messages - local
    Route::get("/messages", [MessageController::class, 'index'])->name('message.index');


    // 11.2: messages - global
    Route::get("/messages-global", [MessageController::class, 'indexGlobal'])->name('messageGlobal.index');







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




    // 13.2: single Receiver
    Route::get("/users/{id}/receivers/{receiverId}", [UserController::class, 'singleReceiver']);







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
        Route::post("/companies/store", [CompanyController::class, 'store'])->name('company.store');
        Route::patch("/companies/update", [CompanyController::class, 'update'])->name('company.update');
        Route::delete("/companies/{id}/delete", [CompanyController::class, 'delete'])->name('company.delete');





        // ========================================================
        // ========================================================


        // 5: units

        // store - update
        Route::post("/units/store", [UnitController::class, 'store'])->name('unit.store');
        Route::patch("/units/update", [UnitController::class, 'update'])->name('unit.update');
        Route::delete("/units/{id}/delete", [UnitController::class, 'delete'])->name('unit.delete');





        // ========================================================
        // ========================================================


        // 6: employees

        // store - update
        Route::post("/employees/store", [EmployeeController::class, 'store'])->name('employee.store');
        Route::patch("/employees/update", [EmployeeController::class, 'update'])->name('employee.update');
        Route::patch("/employees/reset-password", [EmployeeController::class, 'resetPassword'])->name('employee.resetPassword');

        Route::delete("/employees/{id}/delete", [EmployeeController::class, 'delete'])->name('employee.delete');

        // toggle active
        Route::patch("/employees/{id}/toggle-active", [EmployeeController::class, 'toggleActive'])->name('employee.toggleActive');






        // ========================================================
        // ========================================================


        // 7: Help

        // 7.1: update media - address
        Route::patch("/help/media/update", [HelpController::class, 'updateMedia'])->name('help.updateMedia');
        Route::post("/help/address/update", [HelpController::class, 'updateAddress'])->name('help.updateAddress');


        // 7.1: store - update about paragraphs
        Route::post("/help/about/store", [HelpController::class, 'storeAbout'])->name('help.storeAbout');
        Route::patch("/help/about/update", [HelpController::class, 'updateAbout'])->name('help.updateAbout');
        Route::delete("/help/about/{id}/delete", [HelpController::class, 'deleteAbout'])->name('help.deleteAbout');






        // ========================================================
        // ========================================================


        // 8: contact

        // 8.1: update
        Route::patch("/contact/{countryId}/update", [ContactController::class, 'update'])->name('contact.update');

        Route::patch("/contact/{countryId}/update-service", [ContactController::class, 'updateService'])->name('contact.updateService');


        // 8.2: contact phone => store - update - delete
        Route::post("/contact/{countryId}/phones/store", [ContactController::class, 'storePhone'])->name('contact.storePhone');
        Route::patch("/contact/{countryId}/phones/update", [ContactController::class, 'updatePhone'])->name('contact.updatePhone');
        Route::delete("/contact/{countryId}/phones/{id}/delete", [ContactController::class, 'deletePhone'])->name('contact.deletePhone');

        

        // 8.3: contact terms & conditions => store - update
        Route::post("/contact/{countryId}/terms/store", [ContactController::class, 'storeTerm'])->name('contact.storeTerm');
        Route::patch("/contact/{countryId}/terms/update", [ContactController::class, 'updateTerm'])->name('contact.updateTerm');
        Route::delete("/contact/{countryId}/terms/{id}/delete", [ContactController::class, 'deleteTerm'])->name('contact.deleteTerm');







        // ========================================================
        // ========================================================


        // 9: pickup

        // ::special - stop receiving in all stores
        Route::patch("/pickup/toggle-receiving", [PickupController::class, 'toggleReceiving'])->name('pickup.toggleReceiving');

        // store - update
        Route::post("/pickup/store", [PickupController::class, 'store'])->name('pickup.store');

        Route::post("/pickup/{id}/update", [PickupController::class, 'update'])->name('pickup.update');
        Route::delete("/pickup/{id}/delete", [PickupController::class, 'delete'])->name('pickup.delete');

        // toggle active
        Route::patch("/pickup/{id}/toggle-active", [PickupController::class, 'toggleActive'])->name('pickup.toggleActive');




        // 9.1: pickup conditions

        Route::post("/pickup/conditions/store", [PickupController::class, 'storeCondition'])->name('pickup.storeCondition');
        Route::post("/pickup/conditions/update", [PickupController::class, 'updateCondition'])->name('pickup.updateCondition');
        Route::delete("/pickup/conditions/{id}/delete", [PickupController::class, 'deleteCondition'])->name('pickup.deleteCondition');











        // ========================================================
        // ========================================================


        // 10: delivery

        // ::special - stop delivery in all areas
        Route::patch("/delivery/toggle-delivery", [DeliveryController::class, 'toggleDelivery'])->name('delivery.toggleDelivery');

        // store - update
        Route::post("/delivery/store", [DeliveryController::class, 'store'])->name('delivery.store');

        Route::patch("/delivery/{id}/update", [DeliveryController::class, 'update'])->name('delivery.update');
        Route::delete("/delivery/{id}/delete", [DeliveryController::class, 'delete'])->name('delivery.delete');

        // toggle active
        Route::patch("/delivery/{id}/toggle-active", [DeliveryController::class, 'toggleActive'])->name('delivery.toggleActive');




        // 10.1: delivery conditions

        Route::post("/delivery/conditions/store", [DeliveryController::class, 'storeCondition']);
        
        Route::post("/delivery/conditions/update", [DeliveryController::class, 'updateCondition']);
        Route::delete("/delivery/conditions/{id}/delete", [DeliveryController::class, 'deleteCondition']);









        // ========================================================
        // ========================================================


        // 11.1: messages - local


        // update - toggle
        Route::patch("/messages/update", [MessageController::class, 'update'])->name('message.update');
        Route::patch("/messages/toggle-active", [MessageController::class, 'toggleActive'])->name('message.toggleActive');



        // 11.2: messages - global


        // update - toggle
        Route::patch("/messages-global/update", [MessageController::class, 'updateGlobal'])->name('messageGlobal.update');
        Route::patch("/messages-global/toggle-active", [MessageController::class, 'toggleActiveGlobal'])->name('messageGlobal.toggleActive');







        // ========================================================
        // ========================================================


        // 12: products

        // global edits - prices / quantity
        Route::patch("/products/shorthand/update", [ProductController::class, 'updateShorthand']);


        // create - store
        Route::post("/products/store", [ProductController::class, 'store']);

        // edit - update
        Route::post("/products/{id}/update", [ProductController::class, 'update']);


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


