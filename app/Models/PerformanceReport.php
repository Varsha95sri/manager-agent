<?php
// app/Models/PerformanceReport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'team_productivity',
        'top_performers',
        'attention_required',
        'risks',
        'full_report',
    ];

    protected $casts = [
        'top_performers' => 'array',
        'attention_required' => 'array',
        'risks' => 'array',
        'team_productivity' => 'integer',
        'report_date' => 'date',
    ];
}
