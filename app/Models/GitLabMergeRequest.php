<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitLabMergeRequest extends Model
{
    use HasFactory;

    protected $table = 'gitlab_merge_requests';

    protected $fillable = [
        'project_id',
        'employee_id',
        'gitlab_mr_id',
        'state',
        'title',
        'merged_at',
    ];

    protected $casts = [
        'merged_at' => 'datetime',
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
