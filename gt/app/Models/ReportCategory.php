<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'report_type',
        'order',
    ];

    public function questions()
    {
        return $this->hasMany(ReportQuestion::class, 'report_category_id')->orderBy('order');
    }
}
