<?php

namespace App\Models;

class SuratKesehatanJiwa extends MedicalRecordModel
{
    protected $table = 'surat_kesehatan_jiwa';

    protected $primaryKey = 'patient_id';

    public $incrementing = false;
}
