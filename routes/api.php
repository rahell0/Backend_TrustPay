<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PinController;
use App\Http\Controllers\Api\SaldoController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\EwalletController;

// TEST API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API TrustPay berhasil tersambung!'
    ]);
});

// REGISTER
Route::post('/register', [AuthController::class, 'register']);

// LOGIN
Route::post('/login', [AuthController::class, 'login']);

// BUAT PIN
Route::post('/buat-pin', [PinController::class, 'buatPin']);

// TOP UP
Route::post('/topup', [SaldoController::class, 'topUp']);

// CEK SALDO
Route::get('/saldo/{id}', [SaldoController::class, 'cekSaldo']);

// TRANSFER NASIONAL
Route::post('/transfer', [TransferController::class, 'transferNasional']);

// RIWAYAT TRANSAKSI
Route::get('/riwayat/{id}', [TransferController::class, 'riwayatTransaksi']);

// TRANSFER INTERNASIONAL
Route::post('/transfer-internasional', [TransferController::class, 'transferInternasional']);

//TopUp E-Wallet
Route::post('/topup-ewallet', [EwalletController::class, 'topUpEwallet']);