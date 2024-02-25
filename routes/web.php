<?php

use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// superadmin routes
Route::group(['prefix' => 'superadmin/', 'middleware' => ['role:superadmin']], function () {
    Route::get('/superhome', [App\Http\Controllers\SuperadminController::class, 'index'])->name('superadmindashboard');
    // User Crud routes
    Route::get('/superhome/users', [App\Http\Controllers\SuperadminController::class, 'users'])->name('users');
    Route::post('/superhome/register', [App\Http\Controllers\SuperadminController::class, 'registerUser'])->name('registerUser');
    Route::get('/superhome/users/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'edituser']);
    Route::patch('/superhome/users/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updateuser']);
    Route::patch('/superhome/users/deleteuser/{id}', [App\Http\Controllers\SuperadminController::class, 'deleteuser'])->name('delete-user');

    //Role Crud routes
    Route::get('/superhome/roles', [App\Http\Controllers\SuperadminController::class, 'roles'])->name('roles');
    Route::post('/superhome/registerRole', [App\Http\Controllers\SuperadminController::class, 'registerRole'])->name('registerRole');
    Route::get('/superhome/roles/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editrole']);
    Route::patch('/superhome/roles/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updaterole']);
    Route::patch('/superhome/roles/deleterole/{id}', [App\Http\Controllers\SuperadminController::class, 'deleterole'])->name('delete-role');

    //Brand Crud routes
    Route::get('/superhome/brands', [App\Http\Controllers\SuperadminController::class, 'brands'])->name('brands');
    Route::post('/superhome/registerBrand', [App\Http\Controllers\SuperadminController::class, 'registerBrand'])->name('registerBrand');
    Route::get('/superhome/brands/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editbrand']);
    Route::patch('/superhome/brands/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updatebrand']);
    Route::patch('/superhome/brands/deletebrand/{id}', [App\Http\Controllers\SuperadminController::class, 'deletebrand'])->name('delete-brand');
    
    //Brand Crud routes
    Route::get('/superhome/categories', [App\Http\Controllers\SuperadminController::class, 'categories'])->name('categories');
    Route::post('/superhome/registerCategory', [App\Http\Controllers\SuperadminController::class, 'registerCategory'])->name('registerCategory');
    Route::get('/superhome/categories/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editcategory']);
    Route::patch('/superhome/categories/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updatecategory']);
    Route::patch('/superhome/categories/deletecategory/{id}', [App\Http\Controllers\SuperadminController::class, 'deletecategory'])->name('delete-category');

    //Product Crud routes
    Route::get('/superhome/products', [App\Http\Controllers\SuperadminController::class, 'products'])->name('products');
    Route::get('/superhome/products/productForm', [App\Http\Controllers\SuperadminController::class, 'productForm'])->name('productForm');
    Route::post('/superhome/products/productFormSave', [App\Http\Controllers\SuperadminController::class, 'productFormSave'])->name('productFormSave');
    Route::get('/superhome/products/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editproduct']);
    Route::post('/superhome/products/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updateproduct']);
    Route::patch('/superhome/products/deleteproduct/{id}', [App\Http\Controllers\SuperadminController::class, 'deleteproduct'])->name('delete-product');


    //Product Type Crud routes
    Route::get('/superhome/product_type', [App\Http\Controllers\SuperadminController::class, 'product_type'])->name('product_type');
    Route::post('/superhome/registerProductType', [App\Http\Controllers\SuperadminController::class, 'registerProductType'])->name('registerProductType');
    Route::get('/superhome/product_type/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editproduct_type']);
    Route::patch('/superhome/product_type/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updateproduct_type']);
    Route::patch('/superhome/product_type/deleteproduct_type/{id}', [App\Http\Controllers\SuperadminController::class, 'deleteproduct_type'])->name('delete-product_type');

    //Product Family Crud routes
    Route::get('/superhome/product_family', [App\Http\Controllers\SuperadminController::class, 'product_family'])->name('product_family');
    Route::post('/superhome/registerProductFamily', [App\Http\Controllers\SuperadminController::class, 'registerProductFamily'])->name('registerProductFamily');
    Route::get('/superhome/product_family/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editproduct_family']);
    Route::patch('/superhome/product_family/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updateproduct_family']);
    Route::patch('/superhome/product_family/deleteproduct_family/{id}', [App\Http\Controllers\SuperadminController::class, 'deleteproduct_family'])->name('delete-product_family');

    //Product Color Crud routes
    Route::get('/superhome/colors', [App\Http\Controllers\SuperadminController::class, 'colors'])->name('colors');
    Route::post('/superhome/registerColor', [App\Http\Controllers\SuperadminController::class, 'registerColor'])->name('registerColor');
    Route::get('/superhome/colors/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editcolor']);
    Route::patch('/superhome/colors/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updatecolor']);
    Route::patch('/superhome/colors/deletecolor/{id}', [App\Http\Controllers\SuperadminController::class, 'deletecolor'])->name('delete-color');

    //Product Sizes Crud routes
    Route::get('/superhome/sizes', [App\Http\Controllers\SuperadminController::class, 'sizes'])->name('sizes');
    Route::post('/superhome/registerSize', [App\Http\Controllers\SuperadminController::class, 'registerSize'])->name('registerSize');
    Route::get('/superhome/sizes/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editsize']);
    Route::patch('/superhome/sizes/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updatesize']);
    Route::patch('/superhome/sizes/deletesize/{id}', [App\Http\Controllers\SuperadminController::class, 'deletesize'])->name('delete-size');

});
Route::group(['prefix' => 'app/', 'middleware' => ['role:user']], function () {
    Route::get('/userhome', [App\Http\Controllers\UserController::class, 'index'])->name('userdashboard');
    Route::get('/userhome/products', [App\Http\Controllers\UserController::class, 'products'])->name('store');

});