<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReportAnswer;
use Modules\User\Infrastructure\Models\User;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'reporter_id',
        'period_month',
        'period_year',
        'status',
        'created_by',
        'santri_id',
        'pjgt_id',
        'lembaga_id',
    ];

    public function answers()
    {
        return $this->hasMany(ReportAnswer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reporterGt()
    {
        return $this->belongsTo(\App\Models\Santri::class, 'reporter_id');
    }

    public function reporterPjgt()
    {
        return $this->belongsTo(\App\Models\Pjgt::class, 'reporter_id');
    }

    public function santri()
    {
        return $this->belongsTo(\App\Models\Santri::class, 'santri_id');
    }

    public function pjgt()
    {
        return $this->belongsTo(\App\Models\Pjgt::class, 'pjgt_id');
    }

    public function lembaga()
    {
        return $this->belongsTo(\App\Models\Lembaga::class, 'lembaga_id');
    }

    public function getReporterNameAttribute()
    {
        if ($this->report_type === 'gt') {
            return $this->reporterGt?->nama ?? ('Unknown GT ID ' . $this->reporter_id);
        } elseif ($this->report_type === 'pjgt') {
            return $this->reporterPjgt?->nama ?? ('Unknown PJGT ID ' . $this->reporter_id);
        } elseif ($this->report_type === 'korwil') {
            $pjgt = $this->reporterPjgt;
            if ($pjgt) {
                $wilayahs = \App\Models\Wilayah::where('pjgt_id', $pjgt->id)->pluck('nama')->toArray();
                $wilayahStr = count($wilayahs) > 0 ? ' (Korwil ' . implode(', ', $wilayahs) . ')' : ' (Korwil)';
                return $pjgt->nama . $wilayahStr;
            }
            return 'Unknown Korwil ID ' . $this->reporter_id;
        }
        return 'Unknown Role';
    }
    
    // allow appending the reporter_name in array/json
    protected $appends = ['reporter_name'];
}
