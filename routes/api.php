<?php

use Illuminate\Support\Facades\Route;

// Import Seluruh Controller Backend TrustPay
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

/*
|--------------------------------------------------------------------------
| API Routes - TrustPay.id Centralized System
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. RUTE PUBLIK (Akses Bebas Tanpa Login / Tanpa Bearer Token)
// =========================================================================
Route::post('/register', [AuthController::class, 'register']);

// Memproses login multi-role (User & Admin)
// Admin: admin / trustpay2026 | Nasabah: 081362267690 / Nasabah123@
Route::post('/login', [AuthController::class, 'login']);

// Jalur khusus pengecekan notifikasi sistem lintas device
Route::get('/notifikasi', [NotifikasiController::class, 'getNotifikasi']);


// =========================================================================
// 2. RUTE PROTEKSI (Wajib Membawa Bearer Token Sanctum)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth Common
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ---------------------------------------------------------------------
    // JALUR KHUSUS NASABAH / USER (Sesuai User UI.pdf)
    // ---------------------------------------------------------------------
    Route::middleware('role:user')->group(function () {
        // Fitur Keamanan PIN & Saldo
        Route::post('/pin/buat', [PinController::class, 'buatPin']);
        Route::post('/saldo/topup', [SaldoController::class, 'topUp']);
        
        // Transaksi Dompet Digital & Transfer
        Route::post('/ewallet/topup', [EwalletController::class, 'topUpEwallet']);
        Route::post('/transfer/nasional', [TransferController::class, 'transferNasional']);
        Route::post('/transfer/internasional', [TransferController::class, 'transferInternasional']);
        
        // Kurs & Visualisasi Grafik Dashboard Nasabah
        Route::get('/exchange/kurs', [ExchangeController::class, 'index']);
        Route::post('/exchange/kalkulasi', [ExchangeController::class, 'hitungKalkulasi']);
        Route::get('/insight/grafik', [InsightController::class, 'getInsightData']);
        
        // Layanan Bantuan Nasabah
        Route::get('/pusat-bantuan/faq', [PusatBantuanController::class, 'getFaq']);
        Route::post('/pusat-bantuan/keluhan', [PusatBantuanController::class, 'kirimKeluhan']);
    });

    // ---------------------------------------------------------------------
    // JALUR SUPER AMAN KENDALI ADMIN (Sesuai admin UI.pdf)
    // ---------------------------------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Halaman 1: Dashboard Utama Admin (Statistik Box & Log Aktivitas)
        Route::get('/dashboard-stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/reports', [AdminController::class, 'getGlobalReports']);
        
        // Halaman 2: User Management (Tabel Manajemen 20 Dummy Nasabah)
        Route::get('/users', [AdminController::class, 'getUserManagement']);
        
        // Halaman 3: Currency Exchange Admin (Pantau & Update Nilai Kurs)
        Route::get('/kurs', [ExchangeController::class, 'index']);
        Route::post('/kurs/update', [ExchangeController::class, 'updateKurs']);
        
        // Halaman 4: Help Center Admin (Balas & Selesaikan Keluhan Clara Angelica)
        Route::get('/pusat-bantuan/keluhan', [PusatBantuanController::class, 'getKeluhanAdmin']);
        Route::post('/faq', [PusatBantuanController::class, 'tambahFaq']);
        Route::put('/faq/{id}', [PusatBantuanController::class, 'editFaq']);
        Route::delete('/faq/{id}', [PusatBantuanController::class, 'hapusFaq']);
    });

});