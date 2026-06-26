<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'gitlab_project_id',
        'gitlab_repo_url',
        'status',
        'progress_percent',
        'deadline',
        'risk_level',
        'health_score',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($project) {
            $project->calculateRiskAndHealth();
        });
    }

    public function calculateRiskAndHealth(): void
    {
        // Calculate Risk
        if ($this->deadline && $this->status !== 'completed' && $this->status !== 'archived') {
            if (now()->isAfter($this->deadline) && $this->progress_percent < 80) {
                $this->risk_level = 'high';
            } elseif (now()->addDays(7)->isAfter($this->deadline) && $this->progress_percent < 50) {
                $this->risk_level = 'medium';
            } else {
                $this->risk_level = 'low';
            }
        } elseif ($this->status === 'completed') {
            $this->risk_level = 'low';
        }

        // Calculate Health Score (simple heuristic 0-100)
        $score = 100;
        
        if ($this->risk_level === 'high') $score -= 30;
        if ($this->risk_level === 'medium') $score -= 15;
        if ($this->status === 'on_hold') $score -= 20;
        
        // Boost for good progress
        if ($this->progress_percent >= 80) $score += 10;
        
        // Ensure within 0-100 bounds
        $this->health_score = max(0, min(100, $score));
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function commits(): HasMany
    {
        return $this->hasMany(Commit::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(ProjectAllocation::class);
    }
}
