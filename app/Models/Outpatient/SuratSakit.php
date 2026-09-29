<?php

namespace App\Models\Outpatient;

class SuratSakit extends OutpatientLetter
{
    protected $table = 'rj_surat_sakit';

    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];

    protected $appends = ['days'];

    public function getDaysAttribute(): ?int
    {
        return $this->start_date && $this->end_date
            ? (int) $this->start_date->diffInDays($this->end_date) + 1
            : null;
    }
}
