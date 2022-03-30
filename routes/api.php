<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DetailTransaksiController;
use App\Http\Controllers\OutletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [AuthController::class,'login']);


Route::group(['middleware' => ['jwt.verify:admin,kasir,owner']], function() {
    Route::get('login/check',[AuthController::class,'loginCheck']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('report', [TransaksiController::class, 'report']);
});

//UNTUK ADMIN DAN KASIR
Route::group(['middleware' => ['jwt.verify:admin,kasir']], function ()
{
    //MEMBER
    Route::get('member', [MemberController::class,'getAll']);
    Route::post('member', [MemberController::class,'store']);
    Route::get('member/{id}', [MemberController::class,'show']);
    Route::put('member/{id}', [MemberController::class,'update']);
    Route::delete('member/{id}', [MemberController::class,'destroy']);
    Route::get('get_member/{id}', [MemberController::class,'cari_data']);

    //TRANSAKSI
    Route::post('transaksi', [TransaksiController::class,'store']);
    Route::put('transaksi/{id}', [TransaksiController::class,'update']);
    Route::get('transaksi/{id_transaksi}', [TransaksiController::class, 'getById']);
    Route::get('transaksi', [TransaksiController::class, 'getAll']);
    Route::post('transaksi/status/{id_transaksi}', [TransaksiController::class, 'changeStatus']);
    Route::post('transaksi/bayar/{id_transaksi}', [TransaksiController::class, 'bayar']); 
    Route::get('get_transaksi/{id_transaksi}', [TransaksiController::class, 'cari_data']);
     
    //DETAIL TRANSAKSI
    Route::post('transaksi/detail/tambah', [DetailTransaksiController::class, 'store']);
    Route::get('transaksi/detail/{id_transaksi}', [DetailTransaksiController::class, 'getById']);
    Route::get('transaksi/total/{id_detail_transaksi}', [DetailTransaksiController::class, 'getTotal']);


});

//UNTUK ADMIN SAJA
Route::group(['middleware' => ['jwt.verify:admin']], function ()
{
    //OUTLET
    Route::get('outlet', [OutletController::class, 'getAll']);
    Route::get('outlet/{id}', [OutletController::class, 'getById']);
    Route::post('outlet', [OutletController::class, 'store']);
    Route::put('outlet/{id}', [OutletController::class, 'update']);
    Route::delete('outlet/{id}', [OutletController::class, 'delete']);

    //USER
    Route::post('user/tambah', [UserController::class,'store']);
    Route::get('user/{id}', [UserController::class,'show']);
    Route::get('user', [UserController::class,'getAll']);
    Route::put('user/{id}', [UserController::class,'update']);
    Route::delete('user/{id}', [UserController::class,'destroy']);
    Route::get('get_user/{id}', [UserController::class,'cari_data']);

    //PAKET
    Route::post('paket', [PaketController::class,'store']);
    Route::get('paket', [PaketController::class,'getAll']);
    Route::get('paket/{id}', [PaketController::class,'show']);
    Route::put('paket/{id}', [PaketController::class,'update']);
    Route::delete('paket/{id}', [PaketController::class,'destroy']);
});

Route::group(['middleware' => ['jwt.verify:owner']], function() {
    
});





