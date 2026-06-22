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
    ];

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
}
