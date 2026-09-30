<?php

namespace App\Http\Controllers\V5;

use App\Data\Res;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RadiologiController extends Controller
{
    public function getRadiologi($patient_id)
    {
        try {
            $data_radiologi = DB::table('TRadMedik')->where('IDReg', $patient_id)->select(['IDRad', 'IDReg'])->get();
            foreach ($data_radiologi as $key => $value) {
                $data_radiologi[$key]->foto = DB::table('TRadiologiFoto')->where('ID', $value->IDRad)->get();
                $bacaan = DB::select('EXEC [dbo].[RadiologiResultKwit_SP] ?', [$value->IDRad]);
                $bacaan_pacs = $this->getByIdRadiologi($value->IDRad);
                foreach ($bacaan as $k => $v) {
                    $bacaan[$k]->pacs = '';
                    $bacaan[$k]->pacspdf = '';
                    $bacaan[$k]->pacsbarcode = '';

                    $bacaan[$k]->Result = $bacaan[$k]->Result === ''
                        ? $bacaan_pacs[$k]['result']
                        : $bacaan[$k]->Result;

                    $bacaan[$k]->pacs = $bacaan[$k]->pacs === ''
                        ? $bacaan_pacs[$k]['pacs']
                        : $bacaan[$k]->pacs;

                    $bacaan[$k]->pacspdf = $bacaan[$k]->pacspdf === ''
                        ? $bacaan_pacs[$k]['pacspdf']
                        : $bacaan[$k]->pacspdf;

                    $bacaan[$k]->pacsbarcode = $bacaan[$k]->pacsbarcode === ''
                        ? $bacaan_pacs[$k]['pacsbarcode']
                        : $bacaan[$k]->pacsbarcode;
                }

                $bacaan_utf8 = array_map(function ($item) {
                    return array_map(function ($value) {
                        return is_string($value) ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
                    }, (array) $item);
                }, $bacaan);

                $data_radiologi[$key]->bacaan = $bacaan_utf8;
            }

            return response()->json([
                'message' => 'berhasil',
                'data' => $data_radiologi,
            ]);
        } catch (\Throwable $th) {
            return Res::errorJSON($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getByIdRadiologi($id_radiologi)
    {
        try {
            $url = 'http://192.168.1.200:5000/his/new/ERMRadHasil/?idrad='.$id_radiologi;
            $response = Http::get($url);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('gagal mengambil data radiologi');
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            throw new \Exception('Failed to Connect : server web');
        }
    }

    public function fotoRadiologi($id)
    {
        return $this->serveFoto($id, 'inline');
    }

    public function fotoRadiologiDownload($id)
    {
        return $this->serveFoto($id, 'attachment');
    }

    private function serveFoto($id, string $disposition)
    {
        $foto = DB::table('TRadiologiFoto')->where('ID', $id)->first();
        abort_unless($foto && ! empty($foto->name), 404, 'Foto not found');

        $fileName = (string) $foto->name;
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $remoteName = $extension === '' ? $fileName.'.jpg' : $fileName;
        $url = rtrim(config('services.portal_api.document_url'), '/').'/radiologi/'.rawurlencode($remoteName);
        $response = Http::timeout(60)->get($url);

        abort_unless($response->successful(), 404, 'Foto not found');

        return response($response->body(), 200, [
            'Content-Type' => $response->header('Content-Type', 'image/jpeg'),
            'Content-Disposition' => $disposition.'; filename="'.basename($remoteName).'"',
        ]);
    }
}
