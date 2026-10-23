<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class MedicalRecordModel extends Model
{
    protected $connection = 'mysql';
    public $timestamps = false;

    protected $guarded = [];
}
