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
});