<?php

namespace App\Models;

class SuratCutiHamil extends MedicalRecordModel
{
    protected $table = 'surat_cuti_hamil';

    protected $primaryKey = 'patient_id';

    public $incrementing = false;
}
