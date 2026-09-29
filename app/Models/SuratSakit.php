<?php

namespace App\Models;

class SuratSakit extends MedicalRecordModel
{
    protected $table = 'surat_sakits';

    protected $primaryKey = 'patient_id';

    public $incrementing = false;
}
