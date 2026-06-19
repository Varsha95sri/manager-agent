<?php

namespace Database\Factories;

use App\Models\GitCommit;
use App\Models\TeamMember;
use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

class GitCommitFactory extends Factory
{
    protected $model = GitCommit::class;

    public function definition(): array
    {
        return [
            'team_member_id' => TeamMember::factory(),
            'repository_id' => Repository::factory(),
            'commit_hash' => $this->faker->sha1,
            'message' => $this->faker->sentence(8),
            'repository_name' => 'repo-name',
            'committed_at' => $this->faker->dateTimeBetween('2026-06-01', '2026-06-18'),
        ];
    }
}
