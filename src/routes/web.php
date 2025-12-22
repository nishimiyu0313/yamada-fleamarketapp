<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ItemController::class, 'list']);


Route::group(['middleware' => ['auth', 'verified']],function () {
    Route::get('/mylist', [ItemController::class, 'mylist']);
    Route::get('/item/search', [ItemController::class, 'search']);
    Route::post('/item/{item}/like', [ItemController::class, 'like']);
    Route::delete('/item/{item}/unlike', [ItemController::class, 'unlike']);
    Route::get('/items/liked', [ItemController::class, 'likedItems']);
    Route::post('/item/{item}/comments', [ItemController::class, 'commentStore']);
    Route::get('/sell', [ItemController::class, 'index']);
    Route::post('/sell', [ItemController::class, 'sell']);
    Route::get('/mypage/buy', [ItemController::class, 'profileBuy'])->name('item.profilebuy');
    Route::get('/mypage/sell', [ItemController::class, 'profileSell']);


    Route::get('/mypage/profile', [ProfileController::class, 'index']);
    Route::post('/mypage/profile', [ProfileController::class, 'store']);
    Route::get('/mypage/profile/{user_id}', [ProfileController::class, 'profile']);
    Route::post('/mypage/profile/{user_id}', [ProfileController::class, 'updateProfile']);

    Route::get('/purchase/{item_id}', [PaymentController::class, 'index']);
    Route::post('/purchase/{item_id}', [PaymentController::class, 'payment']);
    Route::get('/purchase/address/{item_id}', [ProfileController::class, 'address']);
    Route::post('/purchase/address/{item_id}', [ProfileController::class, 'updateAddress']);
});

Route::get('/item/{item_id}', [ItemController::class, 'detail']);