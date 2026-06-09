<?php
// database/migrations/2026_06_03_090828_create_manager_agent_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->string('github_id')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
            $table->string('title');
            $table->enum('status', ['pending', 'in_progress', 'completed']);
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('git_commits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
            $table->string('commit_hash');
            $table->string('message');
            $table->string('repository_name')->nullable();
            $table->dateTime('committed_at');
            $table->timestamps();
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late']);
            $table->time('check_in')->nullable();
            $table->timestamps();
        });

        Schema::create('meeting_notes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('notes');
            $table->date('meeting_date');
            $table->timestamps();
        });

        Schema::create('performance_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->integer('team_productivity');
            $table->json('top_performers');
            $table->json('attention_required');
            $table->json('risks');
            $table->longText('full_report');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_reports');
        Schema::dropIfExists('meeting_notes');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('git_commits');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('team_members');
    }
};
