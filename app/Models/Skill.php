<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name', 'category'];

    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class, 'team_member_skills')
            ->withPivot('proficiency')
            ->withTimestamps();
    }
}
