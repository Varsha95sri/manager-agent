<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitLabIssue extends Model
{
    use HasFactory;

    protected $table = 'gitlab_issues';

    protected $fillable = [
        'project_id',
        'employee_id',
        'gitlab_issue_id',
        'state',
        'title',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(TeamMember::class, 'employee_id');
    }
}
