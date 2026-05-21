<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EwalletController;
use App\Http\Controllers\Api\PinController;
use App\Http\Controllers\Api\SaldoController;
use App\Http\Controllers\Api\TransferController;

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
    Route::get('/transaksi/riwayat', [TransferController::class, 'riwayatTransaksi']);
});