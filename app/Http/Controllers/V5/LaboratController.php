<?php

namespace App\Http\Controllers\V5;

use App\Data\Res;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LaboratController extends Controller
{
    public function getLaborat($patient_id)
    {
        try {
            $data_lab = DB::table('Laborat as l')
                ->leftJoin('LaboratPasFoto as f', 'f.ID', '=', 'l.IDLab')
                ->where('l.IDReg', $patient_id)
                ->select(['l.IDLab as IDLab', 'l.IDReg as IDReg', 'f.name as pdf'])
                ->get();

            foreach ($data_lab as $key => $value) {
                $result = collect(DB::select('EXEC [dbo].[LaboratResultKwit_SP] ?', [$value->IDLab]));
                $data_lab[$key]->result = $result;
            }

            return response()->json(['message' => 'berhasil', 'data' => $data_lab]);
        } catch (\Throwable $th) {
            return Res::errorJSON($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
