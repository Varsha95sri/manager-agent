<?php
// app/Models/TeamMember.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'role',
        'github_id',
        'task_title',
        'task_commit',
        'attendance',
        'meeting_date',
        'meeting_title',
        'task_assign_date',
        'due_date',
        'login_timing',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_team_member');
    }

    public function meetingNotes()
    {
        return $this->belongsToMany(MeetingNote::class, 'meeting_note_team_member');
    }

    public function gitCommits(): HasMany
    {
        return $this->hasMany(GitCommit::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
