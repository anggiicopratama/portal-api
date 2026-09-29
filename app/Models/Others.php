<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Others extends MedicalRecordModel
{
    public const CATEGORY_ECG = 'ecg';

    protected $table = 'others';

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}
