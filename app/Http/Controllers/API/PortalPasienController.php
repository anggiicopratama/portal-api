<?php

namespace App\Http\Controllers\API;

use App\Data\ResponseData;
use App\Http\Controllers\Controller;
use App\Models\Others;
use App\Models\Outpatient\SuratKeterangan;
use App\Models\Outpatient\SuratKeteranganKlinisPasien;
use App\Models\Outpatient\SuratKeteranganSehat;
use App\Models\Outpatient\SuratPerintahMrs;
use App\Models\Outpatient\SuratRujukan;
use App\Models\Outpatient\SuratSakit;
use App\Models\SuratCutiHamil;
use App\Models\SuratKesehatanJiwa;
use App\Models\SuratMati;
use App\Models\SuratNarkoba;
use App\Models\SuratSakit as SuratSakitRawatInap;
use App\Models\User;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PortalPasienController extends Controller
{
    public function getEcg(int $patient_id): JsonResponse
    {
        try {
            $others = Others::category(Others::CATEGORY_ECG)
                ->where('patient_id', $patient_id)
                ->get();

            foreach ($others as $item) {
                $path = (string) $item->file_name;
                $item->file_content = $path !== '' && Storage::disk('nas_ftp')->exists($path)
                    ? base64_encode(Storage::disk('nas_ftp')->get($path))
                    : null;
            }

            return ResponseData::success('berhasil mengambil data ecg', $others);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengambil data ECG.', [
                'patient_id' => $patient_id,
                'exception' => $exception,
            ]);

            return ResponseData::error('gagal mengambil data ecg');
        }
    }

    public function getSurat(int $patient_id): JsonResponse
    {
        try {
            $surat = [
                'surat_sakit_rj' => SuratSakit::find($patient_id),
                'surat_sakit_ri' => SuratSakitRawatInap::find($patient_id),
                'surat_rujukan_rj' => SuratRujukan::find($patient_id),
                'surat_perintah_mrs_rj' => SuratPerintahMrs::find($patient_id),
                'surat_keterangan_sehat_rj' => SuratKeteranganSehat::find($patient_id),
                'surat_narkoba' => SuratNarkoba::find($patient_id),
                'surat_kesehatan_jiwa' => SuratKesehatanJiwa::find($patient_id),
                'surat_cuti_hamil' => SuratCutiHamil::find($patient_id),
                'surat_keterangan' => SuratKeterangan::find($patient_id),
                'surat_mati' => SuratMati::find($patient_id),
                'surat_keterangan_klinis_pasien' => SuratKeteranganKlinisPasien::find($patient_id),
                'surat_medical_check_up' => $this->getMcu($patient_id),
            ];

            return ResponseData::success('berhasil mengambil data surat', $surat);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengambil data surat.', [
                'patient_id' => $patient_id,
                'exception' => $exception,
            ]);

            return ResponseData::error('gagal mengambil data surat');
        }
    }

    public function getSkdp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_kartu' => ['required', 'string', 'max:30'],
            'bln' => ['required', 'integer', 'between:1,12'],
            'thn' => ['required', 'integer', 'digits:4'],
        ]);

        try {
            $response = Http::acceptJson()
                ->timeout(30)
                ->get(config('services.portal_api.skdp_url'), [
                    'bln' => $validated['bln'],
                    'thn' => $validated['thn'],
                    'noka' => $validated['no_kartu'],
                    'filter' => 1,
                ]);

            return response()->json($response->json(), $response->status());
        } catch (\Throwable $exception) {
            Log::error('Gagal mengambil SKDP.', ['exception' => $exception]);

            return ResponseData::error('gagal mengambil data SKDP');
        }
    }

    public function filePdf(string $fileName): Response
    {
        return $this->proxyDocument('laborat', $fileName, 'pdf', 'application/pdf');
    }

    public function radiologiFoto(string $fileName): Response
    {
        return $this->proxyDocument('radiologi', $fileName, 'jpg', 'image/jpeg');
    }

    private function getMcu(int $patient_id): ?array
    {
        try {
            $response = Http::acceptJson()
                ->timeout(30)
                ->get(rtrim(config('services.portal_api.mcu_url'), '/').'/'.$patient_id);

            if (! $response->successful()) {
                Log::warning('Data MCU tidak tersedia.', [
                    'patient_id' => $patient_id,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $data = $response->json();
            if (! is_array($data)) {
                return null;
            }

            $doctorId = $data['dokter'] ?? null;
            $data['nama_dokter'] = $doctorId
                ? (string) (User::where('mapping_id_dokter', $doctorId)->value('name') ?? '')
                : '';

            return $data;
        } catch (\Throwable $exception) {
            Log::error('Gagal mengambil MCU.', [
                'patient_id' => $patient_id,
                'exception' => $exception,
            ]);

            return null;
        }
    }

    private function proxyDocument(string $directory, string $fileName, string $extension, string $mimeType): Response
    {
        abort_unless(preg_match('/^[\pL\pN _.-]+$/u', $fileName) === 1, 400, 'Invalid file name.');

        $normalizedName = preg_replace('/\.'.preg_quote($extension, '/').'$/i', '', $fileName);
        $url = sprintf(
            '%s/%s/%s.%s',
            rtrim(config('services.portal_api.document_url'), '/'),
            $directory,
            rawurlencode($normalizedName),
            $extension,
        );

        try {
            /** @var ClientResponse $upstream */
            $upstream = Http::timeout(60)->get($url);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengakses penyimpanan dokumen.', [
                'directory' => $directory,
                'file' => $normalizedName,
                'exception' => $exception,
            ]);

            abort(502, 'Document server is unavailable.');
        }

        abort_unless($upstream->successful(), $upstream->status() === 404 ? 404 : 502, 'File not found.');

        return response($upstream->body(), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => sprintf('inline; filename="%s.%s"', $normalizedName, $extension),
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
