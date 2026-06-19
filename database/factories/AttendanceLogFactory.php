<?php

namespace Database\Factories;

use App\Models\AttendanceLog;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceLogFactory extends Factory
{
    protected $model = AttendanceLog::class;

    public function definition(): array
    {
        return [
            'team_member_id' => TeamMember::factory(),
            'date' => $this->faker->dateTimeBetween('2026-06-01', '2026-06-18')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['present', 'present', 'present', 'present', 'late', 'absent']),
            'check_in' => '09:00 AM',
        ];
    }
}
