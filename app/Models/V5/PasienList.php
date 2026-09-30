<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class PasienList extends Model
{
    protected $connection = 'medical_sql';

    protected $table = 'PasienList';

    protected $primaryKey = 'RegNum';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
