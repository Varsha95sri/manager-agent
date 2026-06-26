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
        'gitlab_id',
        'gitlab_user_id',
        'gitlab_username',
        'task_title',
        'task_commit',
        'attendance',
        'meeting_date',
        'meeting_title',
        'task_assign_date',
        'due_date',
        'login_timing',
        'performance_score',
        'performance_grade',
        'department_id',
        'designation_id',
        'team_id',
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

    public function commits(): HasMany
    {
        return $this->hasMany(Commit::class, 'employee_id');
    }

    public function mergeRequests(): HasMany
    {
        return $this->hasMany(GitLabMergeRequest::class, 'employee_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(GitLabIssue::class, 'employee_id');
    }

    public function performanceReports()
    {
        return $this->hasMany(PerformanceReport::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'team_member_skills')
            ->withPivot('proficiency')
            ->withTimestamps();
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function projectAllocations(): HasMany
    {
        return $this->hasMany(ProjectAllocation::class);
    }
}
