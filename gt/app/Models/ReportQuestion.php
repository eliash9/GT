<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_category_id',
        'question',
        'type',
        'options',
        'is_required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'report_category_id');
    }
}
