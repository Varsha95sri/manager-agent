<?php
// app/Models/AttendanceLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'date',
        'status',
        'check_in',
        'check_out',
        'leave_type',
    ];

    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function getIsLateArrivalAttribute(): bool
    {
        if ($this->status === 'late') return true;
        if (!$this->check_in) return false;
        
        // Define late arrival threshold e.g. 10:00:00
        return $this->check_in > '10:00:00';
    }

    public function getIsEarlyExitAttribute(): bool
    {
        if (!$this->check_out) return false;
        
        // Define early exit threshold e.g. 17:00:00 (5 PM)
        return $this->check_out < '17:00:00';
    }
}
