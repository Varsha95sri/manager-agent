<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    protected $fillable = ['name', 'description', 'level'];

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
