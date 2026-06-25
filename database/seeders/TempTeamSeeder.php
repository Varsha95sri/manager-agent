<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TempTeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Frontend Team',
                'slug' => 'frontend',
                'description' => 'Responsible for all user-facing features, UI/UX implementation, and client-side logic using modern frontend frameworks and tailored CSS.',
                'status' => 'Excellent',
                'status_color' => 'success',
                'icon_bg' => 'primary',
            ],
            [
                'name' => 'Backend Team',
                'slug' => 'backend',
                'description' => 'Handles API development, database management, cloud infrastructure, and server-side business logic.',
                'status' => 'Good',
                'status_color' => 'warning',
                'icon_bg' => 'warning',
            ],
            [
                'name' => 'QA & Testing',
                'slug' => 'qa',
                'description' => 'Ensures product quality through automated testing pipelines, manual regression testing, and performance testing integrations.',
                'status' => 'Exceptional',
                'status_color' => 'success',
                'icon_bg' => 'danger',
            ]
        ];

        foreach ($teams as $t) {
            Team::firstOrCreate(['slug' => $t['slug']], $t);
        }
    }
}
