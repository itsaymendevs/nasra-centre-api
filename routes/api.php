<?php

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------

Route::group(['middleware' => 'cors'], function () {


    // 1: main-category
    Route::get("/main-categories", [MainCategoryController::class, 'index'])->name('mainCategory.index');

    // store - update - remove
    Route::post("/main-categories/store", [MainCategoryController::class, 'store'])->name('mainCategory.store');
    Route::patch("/main-categories/update", [MainCategoryController::class, 'update'])->name('mainCategory.update');
    Route::delete("/main-categories/{id}/delete", [MainCategoryController::class, 'delete'])->name('mainCategory.delete');

    // sort - updateSort
    Route::get("/main-categories/sort", [MainCategoryController::class, 'sort'])->name('mainCategory.sort');
    Route::patch("/main-categories/sort/update", [MainCategoryController::class, 'updateSort'])->name('mainCategory.updateSort');




    // ========================================================
    // ========================================================


    // 2: sub-category
    Route::get("/sub-categories", [SubCategoryController::class, 'index'])->name('subCategory.index');

    // store - update
    Route::post("/sub-categories/store", [SubCategoryController::class, 'store'])->name('subCategory.store');
    Route::patch("/sub-categories/update", [SubCategoryController::class, 'update'])->name('subCategory.update');
    Route::delete("/sub-categories/{id}/delete", [SubCategoryController::class, 'delete'])->name('subCategory.delete');

    // sort - updateSort
    Route::get("/sub-categories/{mainCategoryId}/sort", [SubCategoryController::class, 'sort'])->name('subCategory.sort');
    Route::patch("/sub-categories/{mainCategoryId}/sort/update", [SubCategoryController::class, 'updateSort'])->name('subCategory.updateSort');




    // ========================================================
    // ========================================================


    // 3: inner-types
    Route::get("/inner-types", [TypeController::class, 'index'])->name('innerType.index');

    // store - update
    Route::post("/inner-types/store", [TypeController::class, 'store'])->name('innerType.store');
    Route::patch("/inner-types/update", [TypeController::class, 'update'])->name('innerType.update');
    Route::delete("/inner-types/{id}/delete", [TypeController::class, 'delete'])->name('innerType.delete');

    // sort - updateSort
    Route::get("/inner-types/{mainCategoryId}/{subCategoryId}/sort", [TypeController::class, 'sort'])->name('innerType.sort');
    Route::patch("/inner-types/{mainCategoryId}/{subCategoryId}/sort/update", [TypeController::class, 'updateSort'])->name('innerType.updateSort');





    // ========================================================
    // ========================================================


    // 4: companies
    Route::get("/companies", [CompanyController::class, 'index'])->name('company.index');

    // store - update
    Route::post("/companies/store", [CompanyController::class, 'store'])->name('company.store');
    Route::patch("/companies/update", [CompanyController::class, 'update'])->name('company.update');
    Route::delete("/companies/{id}/delete", [CompanyController::class, 'delete'])->name('company.delete');





    // ========================================================
    // ========================================================


    // 5: units
    Route::get("/units", [UnitController::class, 'index'])->name('unit.index');

    // store - update
    Route::post("/units/store", [UnitController::class, 'store'])->name('unit.store');
    Route::patch("/units/update", [UnitController::class, 'update'])->name('unit.update');
    Route::delete("/units/{id}/delete", [UnitController::class, 'delete'])->name('unit.delete');





    // ========================================================
    // ========================================================


    // 6: employees
    Route::get("/employees", [EmployeeController::class, 'index'])->name('employee.index');

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
    Route::get("/help", [HelpController::class, 'index'])->name('help.index');

    // 7.1: update media - address
    Route::patch("/help/media/update", [HelpController::class, 'updateMedia'])->name('help.updateMedia');
    Route::patch("/help/address/update", [HelpController::class, 'updateAddress'])->name('help.updateAddress');


    // 7.1: store - update about paragraphs
    Route::post("/help/about/store", [HelpController::class, 'storeAbout'])->name('help.storeAbout');
    Route::patch("/help/about/update", [HelpController::class, 'updateAbout'])->name('help.updateAbout');
    Route::delete("/help/about/{id}/delete", [HelpController::class, 'deleteAbout'])->name('help.deleteAbout');






    // ========================================================
    // ========================================================


    // 8: contact
    Route::post("/contact", [ContactController::class, 'index'])->name('contact.index');

    // 8.1: update
    Route::patch("/contact/update", [ContactController::class, 'update'])->name('contact.update');


    // 8.2: contact phone => store - update - delete
    Route::post("/contact/phones/store", [ContactController::class, 'storePhone'])->name('contact.storePhone');
    Route::patch("/contact/phones/update", [ContactController::class, 'updatePhone'])->name('contact.updatePhone');
    Route::delete("/contact/phones/{id}/delete", [ContactController::class, 'deletePhone'])->name('contact.deletePhone');



    // 8.3: contact terms & conditions => store - update
    Route::post("/contact/terms/store", [ContactController::class, 'storeTerm'])->name('contact.storeTerm');
    Route::patch("/contact/terms/update", [ContactController::class, 'updateTerm'])->name('contact.updateTerm');
    Route::delete("/contact/terms/{id}/delete", [ContactController::class, 'deleteTerm'])->name('contact.deleteTerm');







    // ========================================================
    // ========================================================


    // 9: pickup
    Route::get("/pickup", [PickupController::class, 'index'])->name('pickup.index');

    // ::special - stop receiving in all stores
    Route::patch("/pickup/toggle-receiving", [PickupController::class, 'toggleReceiving'])->name('pickup.toggleReceiving');

    // store - update
    Route::get("/pickup/create", [PickupController::class, 'create'])->name('pickup.create');
    Route::post("/pickup/store", [PickupController::class, 'store'])->name('pickup.store');

    Route::get("/pickup/{id}/edit", [PickupController::class, 'edit'])->name('pickup.edit');
    Route::patch("/pickup/{id}/update", [PickupController::class, 'update'])->name('pickup.update');
    Route::delete("/pickup/{id}/delete", [PickupController::class, 'delete'])->name('pickup.delete');

    // toggle active
    Route::patch("/pickup/{id}/toggle-active", [PickupController::class, 'toggleActive'])->name('pickup.toggleActive');




    // 9.1: pickup conditions
    Route::get("/pickup/conditions", [PickupController::class, 'conditions'])->name('pickup.conditions');

    Route::post("/pickup/conditions/store", [PickupController::class, 'storeCondition'])->name('pickup.storeCondition');
    Route::patch("/pickup/conditions/update", [PickupController::class, 'updateCondition'])->name('pickup.updateCondition');
    Route::delete("/pickup/conditions/{id}/delete", [PickupController::class, 'deleteCondition'])->name('pickup.deleteCondition');











    // ========================================================
    // ========================================================


    // 10: delivery
    Route::get("/delivery", [DeliveryController::class, 'index'])->name('delivery.index');

    // ::special - stop delivery in all areas
    Route::patch("/delivery/toggle-delivery", [DeliveryController::class, 'toggleDelivery'])->name('delivery.toggleDelivery');

    // store - update
    Route::get("/delivery/create", [DeliveryController::class, 'create'])->name('delivery.create');
    Route::post("/delivery/store", [DeliveryController::class, 'store'])->name('delivery.store');

    Route::get("/delivery/{id}/edit", [DeliveryController::class, 'edit'])->name('delivery.edit');
    Route::patch("/delivery/{id}/update", [DeliveryController::class, 'update'])->name('delivery.update');
    Route::delete("/delivery/{id}/delete", [DeliveryController::class, 'delete'])->name('delivery.delete');

    // toggle active
    Route::patch("/delivery/{id}/toggle-active", [DeliveryController::class, 'toggleActive'])->name('delivery.toggleActive');




    // 10.1: delivery conditions
    Route::get("/delivery/conditions", [DeliveryController::class, 'conditions'])->name('delivery.conditions');

    Route::post("/delivery/conditions/store", [DeliveryController::class, 'storeCondition'])->name('delivery.storeCondition');
    Route::patch("/delivery/conditions/update", [DeliveryController::class, 'updateCondition'])->name('delivery.updateCondition');
    Route::delete("/delivery/conditions/{id}/delete", [DeliveryController::class, 'deleteCondition'])->name('delivery.deleteCondition');









    // ========================================================
    // ========================================================


    // 11.1: messages - local
    Route::get("/messages", [MessageController::class, 'index'])->name('message.index');


    // update - toggle
    Route::patch("/messages/update", [MessageController::class, 'update'])->name('message.update');
    Route::patch("/messages/toggle-active", [MessageController::class, 'toggleActive'])->name('message.toggleActive');



    // 11.2: messages - global
    Route::get("/messages-global", [MessageController::class, 'indexGlobal'])->name('messageGlobal.index');


    // update - toggle
    Route::patch("/messages-global/update", [MessageController::class, 'updateGlobal'])->name('messageGlobal.update');
    Route::patch("/messages-global/toggle-active", [MessageController::class, 'toggleActiveGlobal'])->name('messageGlobal.toggleActive');







    // ========================================================
    // ========================================================


    // 12: products
    Route::get("/products", [ProductController::class, 'index'])->name('product.index');

    // global edits - prices / quantity
    Route::get("/products/shorthand", [ProductController::class, 'shorthand'])->name('product.shorthand');
    Route::patch("/products/shorthand/update", [ProductController::class, 'updateShorthand'])->name('product.updateShorthand');


    // create - store
    Route::get("/products/create", [ProductController::class, 'create'])->name('product.create');
    Route::post("/products/store", [ProductController::class, 'store'])->name('product.store');

    // edit - update
    Route::get("/products/{id}/edit", [ProductController::class, 'edit'])->name('product.edit');
    Route::patch("/products/{id}/update", [ProductController::class, 'update'])->name('product.update');


    // toggle show / home
    Route::patch("/products/{id}/toggle-home", [ProductController::class, 'toggleHome'])->name('product.toggleHome');

    Route::patch("/products/{id}/toggle-show", [ProductController::class, 'toggleShow'])->name('product.toggleShow');



    // 12.1: sort - updateSort (based on type)
    Route::get("/products/{type}/sort", [ProductController::class, 'typeSort'])->name('product.typeSort');
    Route::patch("/products/{type}/sort/update", [ProductController::class, 'updateTypeSort'])->name('product.updateTypeSort');
    

    // 12.2: sort - updateSort (based on category)
    Route::get("/products/{mainCategoryId}/{subCategoryId}/{typeId}/sort", [ProductController::class, 'sort'])->name('product.sort');
    Route::patch("/products/{mainCategoryId}/{subCategoryId}/{typeId}/sort/update", [ProductController::class, 'updateSort'])->name('product.updateSort');



});

// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
