<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Therapy extends Model
{
    use HasFactory;

    protected $connection = 'medical_sql';

    protected $table = 'Therapy';

    public $timestamps = false;

    protected $fillable = [
        'ID', 'Register', 'Tanggal', 'Therapy', 'Lama', 'FollowUp', 'Jam_masuk', 'Biaya',
        'Diagnosa_Awal', 'Rujukan', 'Details', 'Layanan', 'SubLayanan', 'TriageID',
        'KasirID', 'TglByr', 'DownPay', 'JasaPrk', 'ByPS', 'DokterID', 'Shift', 'Usr',
        'NewPx', 'Requestpx', 'uPx', 'PxNo', 'TGL', 'Printed', 'Disc1', 'Disc2', 'Penj',
        'Dibyr', 'ID2', 'Debitur', 'Phk3', 'jam_praktek', 'xID', 'SummaryID', 'userid',
        'NoSEP', 'NoRef', 'JenRef', 'Tgl_rujukan', 'NoRujukan', 'JenReq', 'PoliEksekutif',
        'Norujukanpoli', 'KelasBPJS', 'id_online', 'NoWA', 'Datang', 'SensusIRJ',
        'OnlineID', 'begin_time', 'end_time', 'posted', 'IDN', 'canceled',
    ];

    protected $casts = [
        'Tanggal' => 'date:Y-m-d',
        'Jam_masuk' => 'date:H:i:s',
    ];

    public function spesialis()
    {
        return $this->belongsTo(Specialist::class, 'SubLayanan', 'Spesialis')->select('Spesialis', 'ID', 'KdBPJS');
    }

    public function PasienList()
    {
        return $this->belongsTo(PasienList::class, 'Register', 'RegNum')->select('RegNum', 'Nama', 'Tanggal_Lahir', 'Addr', 'telepon', 'NoJKN');
    }

    public function Dokter()
    {
        return $this->belongsTo(Dokter::class, 'DokterID', 'ID')->select('ID', 'Dokter', 'SpecialisID', 'kode_dpjp AS KodeBpjs');
    }

    public function upx()
    {
        return $this->belongsTo(Upx::class, 'uPx', 'ID')->select(['ID', 'PxRS']);
    }

    public function scopeSearch($query, Request $request)
    {
        $query->when($request->filled('id'), fn ($q) => $q->where('Therapy.id', $request->id));
        $query->when($request->filled('no_jkn'), fn ($q) => $q->where('Therapy.NoJKN', $request->nojkn));
        $query->when($request->filled('nosep'), fn ($q) => $q->where('NoSep', 'like', '%'.$request->nosep.'%'));
        $query->when($request->filled('nama'), function ($q) use ($request) {
            return $q->whereHas('PasienList', fn ($subQuery) => $subQuery->where('Nama', 'like', '%'.$request->nama.'%'));
        });
        $query->when($request->filled('alamat'), function ($q) use ($request) {
            return $q->whereHas('PasienList', fn ($subQuery) => $subQuery->where('Addr', 'like', '%'.$request->alamat.'%'));
        });

        return $query;
    }
}
