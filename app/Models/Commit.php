<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commit extends Model
{
    use HasFactory;

    protected $table = 'commits';

    protected $fillable = [
        'project_id',
        'employee_id',
        'commit_sha',
        'message',
        'commit_url',
        'committed_at',
    ];

    protected $casts = [
        'committed_at' => 'datetime',
    ];

    /**
     * Get the project that owns the commit.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the employee (team member) that made the commit.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'employee_id');
    }
}
