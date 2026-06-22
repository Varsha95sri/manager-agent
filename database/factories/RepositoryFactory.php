<?php

namespace Database\Factories;

use App\Models\Repository;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepositoryFactory extends Factory
{
    protected $model = Repository::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => 'repo-' . $this->faker->slug(2),
            'url' => 'https://gitlab.com/org/' . $this->faker->slug(2),
        ];
    }
}
