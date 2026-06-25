<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'description'];

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }
}
