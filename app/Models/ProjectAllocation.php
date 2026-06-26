<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAllocation extends Model
{
    protected $fillable = [
        'project_id',
        'team_member_id',
        'allocated_from',
        'allocated_to',
        'role_on_project',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'allocated_from' => 'date',
            'allocated_to' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }
}
