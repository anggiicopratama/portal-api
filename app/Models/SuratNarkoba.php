<?php

namespace App\Models;

class SuratNarkoba extends MedicalRecordModel
{
    protected $table = 'surat_narkobas';

    protected $primaryKey = 'patient_id';

    public $incrementing = false;
}
