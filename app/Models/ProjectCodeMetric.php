<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCodeMetric extends Model
{
    use HasFactory;

    protected $table = 'project_code_metrics';

    protected $fillable = [
        'project_id',
        'code_quality_score',
        'technical_debt_score',
        'security_score',
        'test_coverage_score',
        'bug_density_score',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
