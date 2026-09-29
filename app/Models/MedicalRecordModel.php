<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class MedicalRecordModel extends Model
{
    public $timestamps = false;

    protected $guarded = [];
}
