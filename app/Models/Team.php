<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'lead_id',
        'description',
        'status',
        'status_color',
        'icon_bg',
    ];

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function performanceReports()
    {
        return $this->hasMany(PerformanceReport::class);
    }
}
