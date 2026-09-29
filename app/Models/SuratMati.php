<?php

namespace App\Models;

class SuratMati extends MedicalRecordModel
{
    protected $table = 'surat_matis';

    protected $primaryKey = 'patient_id';

    public $incrementing = false;
}
