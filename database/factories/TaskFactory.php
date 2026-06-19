<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'team_member_id' => TeamMember::factory(),
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(6),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'due_date' => $this->faker->dateTimeBetween('2026-06-01', '2026-06-18')->format('Y-m-d'),
        ];
    }
}
