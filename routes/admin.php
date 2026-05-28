<?php

use Illuminate\Support\Facades\Route;

// Import Controller yang dibutuhkan Admin
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\PusatBantuanController;

/*
|--------------------------------------------------------------------------
| Admin API Routes - TrustPay.id
|--------------------------------------------------------------------------
| Semua rute di file ini otomatis dilindungi oleh sistem token (Sanctum)
| dan pengecekan role Admin, jadi sangat aman dari nasabah biasa.
*/

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    /**
          * FITUR EXCHANGE: UPDATE NILAI KURS VALAS
     * URL Akses: POST /api/admin/exchange/kurs/update
     */
    Route::post('/exchange/kurs/update', [ExchangeController::class, 'updateKurs']);


    /**
     * FITUR PUSAT BANTUAN: KENDALI DATA FAQ
     * URL Akses: 
     * - POST   /api/admin/faq (Tambah)
     * - PUT    /api/admin/faq/{id} (Edit)
     * - DELETE /api/admin/faq/{id} (Hapus)
     */
    Route::post('/faq', [PusatBantuanController::class, 'tambahFaq']);       // Tambah FAQ Baru
    Route::put('/faq/{id}', [PusatBantuanController::class, 'editFaq']);     // Edit FAQ
    Route::delete('/faq/{id}', [PusatBantuanController::class, 'hapusFaq']); // Hapus FAQ

});