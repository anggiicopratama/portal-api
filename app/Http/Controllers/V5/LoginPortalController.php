<?php

namespace App\Http\Controllers\V5;

use App\Data\Res;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LoginPortalController extends Controller
{
    public function login(Request $request)
    {
        try {
            $no_rm = $request->no_rm;
            $tanggal_lahir = $request->tanggal_lahir;

            $no_rm = DB::table('PasienList')
                ->where('RegNum', $no_rm)
                ->where('Tanggal_Lahir', $tanggal_lahir)
                ->select(['RegNum', 'Nama'])
                ->first();

            $patient_list = DB::table('Therapy')->where('Register', $no_rm->RegNum)->select(['ID', 'Tanggal', 'FollowUp'])->orderBy('Tanggal', 'DESC')->get();
            $data_radiologi = DB::table('TRadMedik')->whereIn('IDReg', $patient_list->pluck('ID'))->select(['IDRad', 'IDReg', 'TRad'])->get();
            $data_lab = DB::table('Laborat as l')
                ->whereIn('l.IDReg', $patient_list->pluck('ID'))
                ->select(['IDLab', 'IDReg', 'TLab'])
                ->get();

            return response()->json([
                'patient_list' => $patient_list,
                'nama_pasien' => $no_rm->Nama,
                'data_radiologi' => $data_radiologi,
                'data_laborat' => $data_lab,
                'message' => 'Data berhasil di dapat',
            ]);
        } catch (\Throwable $th) {
            return Res::errorJSON($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
