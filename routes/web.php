<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('auth.login');
// });

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified']],function () {
    Route::get('/dashboard',[App\Http\Controllers\Admin\DashboardController::class,'dashboard'])->name('dashboard');
    Route::get('change_password', [App\Http\Controllers\Admin\UserController::class,'setting_view'])->name('change_password');
    Route::post('passwordChange', [App\Http\Controllers\Admin\UserController::class,'passwordChange'])->name('passwordChange');
    Route::resource('/user',App\Http\Controllers\Admin\UserController::class);
    Route::post('/user_delete', [App\Http\Controllers\Admin\UserController::class,'user_delete'])->name('user_delete');
    Route::post('/user_status', [App\Http\Controllers\Admin\UserController::class,'user_status'])->name('user_status');

    Route::get('/module',[App\Http\Controllers\Admin\ModuleController::class,'index'])->name('module.index');
    Route::post('/module_store',[App\Http\Controllers\Admin\ModuleController::class,'store'])->name('module.store');
    Route::get('/module/{id}',[App\Http\Controllers\Admin\ModuleController::class,'edit'])->name('module.edit');
    Route::put('/module/{id}',[App\Http\Controllers\Admin\ModuleController::class,'update'])->name('module.update');
    Route::delete('/module/{id}',[App\Http\Controllers\Admin\ModuleController::class,'destroy'])->name('module.destroy');

    Route::resource('/role', App\Http\Controllers\Admin\RoleController::class);
    Route::post('/role_filter', [App\Http\Controllers\Admin\RoleController::class,'filter'])->name('rolefilter');
    Route::post('/role_delete', [App\Http\Controllers\Admin\RoleController::class,'role_delete'])->name('role_delete');

    Route::resource('/general_settings', App\Http\Controllers\Admin\GeneralSettingController::class);

    Route::resource('/category', App\Http\Controllers\Admin\CategoryController::class);
    Route::post('/category/deleteAll', [App\Http\Controllers\Admin\CategoryController::class,'deleteAll'])->name('deleteAll');
    Route::post('/category/rec_update', [App\Http\Controllers\Admin\CategoryController::class,'rec_update'])->name('rec_update');
    Route::get('/loadCategories', [App\Http\Controllers\Admin\CategoryController::class,'loadCategories'])->name('loadCategories');

    Route::resource('/brand', App\Http\Controllers\Admin\BrandController::class);
    Route::post('/brand/deleteAll', [App\Http\Controllers\Admin\BrandController::class,'deleteAll'])->name('deleteAll');
    Route::post('/brand/rec_update', [App\Http\Controllers\Admin\BrandController::class,'rec_update'])->name('rec_update');
    Route::get('/loadBrands', [App\Http\Controllers\Admin\BrandController::class,'loadBrands'])->name('loadBrands');

    Route::resource('/unit', App\Http\Controllers\Admin\UnitController::class);
    Route::post('/unit/deleteAll', [App\Http\Controllers\Admin\UnitController::class,'deleteAll'])->name('deleteAll');
    Route::post('/unit/rec_update', [App\Http\Controllers\Admin\UnitController::class,'rec_update'])->name('rec_update');
    Route::get('/loadUnits', [App\Http\Controllers\Admin\UnitController::class,'loadUnits'])->name('loadUnits');

    Route::resource('/product', App\Http\Controllers\Admin\ProductController::class);
    Route::post('/product/deleteAll', [App\Http\Controllers\Admin\ProductController::class,'deleteAll'])->name('deleteAll');
    Route::post('/product/rec_update', [App\Http\Controllers\Admin\ProductController::class,'rec_update'])->name('rec_update');
    Route::get('/product_search', [App\Http\Controllers\Admin\ProductController::class,'product_search'])->name('product_search');

    Route::resource('/supplier', App\Http\Controllers\Admin\SupplierController::class);
    Route::post('/supplier/deleteAll', [App\Http\Controllers\Admin\SupplierController::class,'deleteAll'])->name('deleteAll');
    Route::post('/supplier/rec_update', [App\Http\Controllers\Admin\SupplierController::class,'rec_update'])->name('rec_update');
    Route::get('/loadSuppliers', [App\Http\Controllers\Admin\SupplierController::class,'loadSuppliers'])->name('loadSuppliers');
    
    Route::resource('/warehouse', App\Http\Controllers\Admin\WarehouseController::class);
    Route::post('/warehouse/deleteAll', [App\Http\Controllers\Admin\WarehouseController::class,'deleteAll'])->name('deleteAll');
    Route::post('/warehouse/rec_update', [App\Http\Controllers\Admin\WarehouseController::class,'rec_update'])->name('rec_update');
    
    Route::resource('/customer', App\Http\Controllers\Admin\CustomerController::class);
    Route::post('/customer/deleteAll', [App\Http\Controllers\Admin\CustomerController::class,'deleteAll'])->name('deleteAll');
    Route::post('/customer/rec_update', [App\Http\Controllers\Admin\CustomerController::class,'rec_update'])->name('rec_update');

    Route::resource('/purchase', App\Http\Controllers\Admin\PurchaseController::class);
    Route::post('/purchase/StorePurchase', [App\Http\Controllers\Admin\PurchaseController::class,'StorePurchase'])->name('StorePurchase');
    Route::get('getPurchaseView', [App\Http\Controllers\Admin\PurchaseController::class,'getPurchaseView'])->name('getPurchaseView');
    Route::post('/purchase/rec_update', [App\Http\Controllers\Admin\PurchaseController::class,'rec_update'])->name('rec_update');
    Route::post('/purchase/storeFinalPurchase', [App\Http\Controllers\Admin\PurchaseController::class,'storeFinalPurchase'])->name('storeFinalPurchase');
    Route::get('/getAverageCostAndSalePrice/{id}', [App\Http\Controllers\Admin\PurchaseController::class,'getAverageCostAndSalePrice'])->name('getAverageCostAndSalePrice');

});