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
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\AdminController; 

// =========================================================================
// RUTE PUBLIK (Akses Bebas Tanpa Login / Tanpa Bearer Token)
// =========================================================================
Route::post('/register', [AuthController::class, 'register']);

// Catatan: Pastikan AuthController@login Anda bisa memproses login untuk USER maupun ADMIN 
// menggunakan data dari DatabaseSeeder (Username: Admin TrustPay / Password: AdminStrictT2026!)
Route::post('/login', [AuthController::class, 'login']);

// PERBAIKAN SINKRON: Jalur publik murni agar frontend bisa tes integrasi lintas device/laptop dengan lancar
Route::get('/notifikasi', [NotifikasiController::class, 'getNotifikasi']);


// =========================================================================
// RUTE PROTEKSI (Wajib Membawa Bearer Token saat hit dari Frontend React)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Profil & Auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Fitur Transaksi Nasabah
    Route::post('/pin/buat', [PinController::class, 'buatPin']);
    Route::post('/saldo/topup', [SaldoController::class, 'topUp']);
    Route::post('/ewallet/topup', [EwalletController::class, 'topUpEwallet']);
    Route::post('/transfer/nasional', [TransferController::class, 'transferNasional']);
    Route::post('/transfer/internasional', [TransferController::class, 'transferInternasional']);
    
    // Fitur Exchange & Grafik User
    Route::get('/exchange/kurs', [ExchangeController::class, 'index']);
    Route::post('/exchange/kalkulasi', [ExchangeController::class, 'hitungKalkulasi']);
    Route::get('/insight/grafik', [InsightController::class, 'getInsightData']);
    
    // Fitur Pusat Bantuan (FAQ & Keluhan)
    Route::get('/pusat-bantuan/faq', [PusatBantuanController::class, 'getFaq']);
    Route::post('/pusat-bantuan/keluhan', [PusatBantuanController::class, 'kirimKeluhan']);
    
    // =========================================================================
    // ENDPOINT UTAMA FRONTEND ADMIN (SINKRON 100% DENGAN UI FIX)
    // =========================================================================
    Route::get('/admin/dashboard-stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/admin/users', [AdminController::class, 'getUserManagement']);
    Route::get('/admin/reports', [AdminController::class, 'getGlobalReports']);
    
    // Manajemen Kurs Jual & Beli (Halaman Currency Exchange Admin)
    Route::get('/admin/kurs', [ExchangeController::class, 'index']);
    Route::post('/admin/kurs/update', [ExchangeController::class, 'updateKurs']);
    
    // Manajemen FAQ & Keluhan Masuk (Halaman Pusat Bantuan Admin)
    Route::get('/admin/pusat-bantuan/keluhan', [PusatBantuanController::class, 'getKeluhanAdmin']);
    Route::post('/pusat-bantuan/faq', [PusatBantuanController::class, 'tambahFaq']);
    Route::put('/pusat-bantuan/faq/{id}', [PusatBantuanController::class, 'editFaq']);
    Route::delete('/pusat-bantuan/faq/{id}', [PusatBantuanController::class, 'hapusFaq']);
});