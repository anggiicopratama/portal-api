<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $connection = 'medical_sql';

    protected $table = 'Specialist';

    protected $primaryKey = 'ID';

    public $timestamps = false;
}
