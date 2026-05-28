<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EwalletController;
use App\Http\Controllers\Api\PinController;
use App\Http\Controllers\Api\SaldoController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\PusatBantuanController;

// Rute Publik (Akses Tanpa Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Proteksi (Wajib Membawa Bearer Token saat hit API)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    
    Route::post('/pin/buat', [PinController::class, 'buatPin']);
    Route::post('/saldo/topup', [SaldoController::class, 'topUp']);
    
    Route::post('/ewallet/topup', [EwalletController::class, 'topUpEwallet']);
    Route::post('/transfer/nasional', [TransferController::class, 'transferNasional']);
    Route::post('/transfer/internasional', [TransferController::class, 'transferInternasional']);
    Route::get('/exchange/kurs', [ExchangeController::class, 'index']);
    Route::post('/exchange/kalkulasi', [ExchangeController::class, 'hitungKalkulasi']);
    Route::get('/transaksi/riwayat', [TransferController::class, 'riwayatTransaksi']);
    Route::get('/insight/grafik', [InsightController::class, 'getInsightData']);
    Route::get('/pusat-bantuan/faq', [PusatBantuanController::class, 'getFaq']);
    Route::post('/pusat-bantuan/keluhan', [PusatBantuanController::class, 'kirimKeluhan']);
    Route::post('/logout', [AuthController::class, 'logout']);
});