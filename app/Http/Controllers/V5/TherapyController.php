<?php

namespace App\Http\Controllers\V5;

use App\Http\Controllers\Controller;
use App\Models\V5\Therapy;
use App\Service\StrResponseService;
use Symfony\Component\HttpFoundation\Response;

class TherapyController extends Controller
{
    public function detailPatientPortal($patient_id)
    {
        $patient = Therapy::select([
            'ID',
            'Register',
            'DokterID',
            'Tanggal',
            'canceled',
            'NoSEP',
            'uPx',
            'NoRujukan',
        ])
            ->with(['PasienList' => function ($query) {
                $query->select(['RegNum', 'Nama', 'Addr', 'Telepon', 'NoJKN', 'Tanggal_Lahir', 'Jenis_Kelamin', 'Jenis_Kelamin', 'NIK']);
            }, 'Dokter', 'upx'])
            ->where('ID', $patient_id)->first();

        return StrResponseService::success(Response::HTTP_OK, 'Berhasil mengambil data therapy', $patient);
    }
}
