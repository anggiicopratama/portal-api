<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PortalPasienController;
use Illuminate\Support\Facades\Route;

Route::post('/token/create', [AuthController::class, 'create']);

Route::middleware('portal.token')->prefix('portal-pasien')->group(function (): void {
    Route::get('/surat/{patient_id}', [PortalPasienController::class, 'getSurat']);
    Route::get('/skdp', [PortalPasienController::class, 'getSkdp']);
    Route::get('/ecg/{patient_id}', [PortalPasienController::class, 'getEcg']);
    Route::get('/pdf/{pdf}', [PortalPasienController::class, 'filePdf'])->name('laborat.pdf');
    Route::get('/foto/{foto}', [PortalPasienController::class, 'radiologiFoto'])->name('radiologi.foto');
});
