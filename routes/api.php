<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PortalPasienController;
use App\Http\Controllers\V5\LaboratController;
use App\Http\Controllers\V5\LoginPortalController;
use App\Http\Controllers\V5\RadiologiController;
use App\Http\Controllers\V5\TherapyController;
use Illuminate\Support\Facades\Route;

Route::post('/token/create', [AuthController::class, 'create']);
Route::post('/v5/token/create', [AuthController::class, 'create']);

Route::middleware('portal.token')->prefix('portal-pasien')->group(function (): void {
    Route::get('/surat/{patient_id}', [PortalPasienController::class, 'getSurat']);
    Route::get('/skdp', [PortalPasienController::class, 'getSkdp']);
    Route::get('/ecg/{patient_id}', [PortalPasienController::class, 'getEcg']);
    Route::get('/pdf/{pdf}', [PortalPasienController::class, 'filePdf'])->name('laborat.pdf');
    Route::get('/foto/{foto}', [PortalPasienController::class, 'radiologiFoto'])->name('radiologi.foto');
});

Route::prefix('v5')->middleware(['portal.token', 'db.medical-sql'])->group(function (): void {
    Route::get('portal-laborat/{patientID}', [LaboratController::class, 'getLaborat']);
    Route::get('portal-radiologi/{patientID}', [RadiologiController::class, 'getRadiologi']);
    Route::get('radiologi/foto/{id}', [RadiologiController::class, 'fotoRadiologi'])
        ->name('v5.radiologi.foto');
    Route::get('radiologi/foto/download/{id}', [RadiologiController::class, 'fotoRadiologiDownload'])
        ->name('v5.radiologi.foto.download');
    Route::post('portal-login', [LoginPortalController::class, 'login']);
    Route::get('portal-pasien/{patientID}', [TherapyController::class, 'detailPatientPortal']);
});
