<?php
// app/Models/MeetingNote.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'notes',
        'meeting_date',
        'meeting_time',
    ];

    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class, 'meeting_note_team_member');
    }
}
