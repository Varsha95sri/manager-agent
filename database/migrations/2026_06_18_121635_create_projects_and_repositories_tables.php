<?php

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
        // Create projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create repositories table
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name');
            $table->string('url')->nullable();
            $table->timestamps();
        });

        // Add nullable project_id to tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
        });

        // Add nullable repository_id to git_commits
        Schema::table('git_commits', function (Blueprint $table) {
            $table->foreignId('repository_id')->nullable()->constrained('repositories')->onDelete('cascade');
        });

        // Add indexes for query performance
        Schema::table('git_commits', function (Blueprint $table) {
            $table->index('committed_at');
            $table->index('team_member_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('due_date');
            $table->index('team_member_id');
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index('date');
            $table->index('team_member_id');
            $table->index('status');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['team_member_id']);
            $table->dropIndex(['date']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['team_member_id']);
            $table->dropIndex(['due_date']);
            $table->dropColumn('project_id');
        });

        Schema::table('git_commits', function (Blueprint $table) {
            $table->dropIndex(['team_member_id']);
            $table->dropIndex(['committed_at']);
            $table->dropColumn('repository_id');
        });

        Schema::dropIfExists('repositories');
        Schema::dropIfExists('projects');
    }
};
