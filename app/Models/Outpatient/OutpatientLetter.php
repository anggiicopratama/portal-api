<?php

namespace App\Models\Outpatient;

use App\Models\MedicalRecordModel;

abstract class OutpatientLetter extends MedicalRecordModel
{
    protected $primaryKey = 'patient_id';

    public $incrementing = false;
}
