<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
    Route::get('/product', [ProductController::class, 'index'])->name('products');
    Route::post('/add', [ProductController::class, 'store'])->name('addproduct');
    Route::post('/update/{id}', [ProductController::class, 'update'])->name('updateproduct');
    Route::post('/delete/{id}', [ProductController::class, 'delete'])->name('deleteproduct');

});

Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
    Route::get('/category', [CategoryController::class, 'index'])->name('categories');
    Route::post('/add', [CategoryController::class, 'store'])->name('addcategory');
    Route::post('/update/{id}', [CategoryController::class, 'update'])->name('updatecategory');
    Route::post('/delete/{id}', [CategoryController::class, 'delete'])->name('deletecategory');

});

Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::post('/register', [UserController::class, 'register'])->name('register');
    Route::post('/login', [UserController::class, 'login'])->name('login');
    Route::post('/resetpassword', [UserController::class, 'resetpassword'])->name('resetpassword');
    Route::post('/confirmemail', [UserController::class, 'confirmemail'])->name('confirmemail');
    Route::post('/changepassword/{id}', [UserController::class, 'changepassword'])->name('changepassword');
});


