<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $connection = 'medical_sql';

    protected $table = 'Dokter';

    protected $primaryKey = 'ID';

    public $timestamps = false;
}
