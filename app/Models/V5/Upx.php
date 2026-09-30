<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class Upx extends Model
{
    protected $connection = 'medical_sql';

    protected $table = 'uPx';

    protected $primaryKey = 'ID';

    public $timestamps = false;
}
