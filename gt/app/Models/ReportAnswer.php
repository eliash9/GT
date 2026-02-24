<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'report_question_id',
        'answer',
    ];

    // Assuming we want to parse array or json answers sometimes, though we define it as text nullable in db
    protected $casts = [
        'answer' => 'string' // can be updated to array/json if we store multiple choice nicely
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function question()
    {
        return $this->belongsTo(ReportQuestion::class, 'report_question_id');
    }
}
