<?php

use App\Http\Controllers\API\PortalPasienController;
use App\Http\Controllers\V5\RadiologiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/surat/{patient_id}', [PortalPasienController::class, 'getSurat']);
// Route::get('/portal-radiologi/{patientID}', [RadiologiController::class, 'getRadiologi']);