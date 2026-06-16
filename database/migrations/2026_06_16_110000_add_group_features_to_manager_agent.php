<?php
// database/migrations/2026_06_16_110000_add_group_features_to_manager_agent.php

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
        // 1. Add meeting_time to meeting_notes
        if (!Schema::hasColumn('meeting_notes', 'meeting_time')) {
            Schema::table('meeting_notes', function (Blueprint $table) {
                $table->string('meeting_time')->nullable();
            });
        }

        // 2. Create meeting_note_team_member pivot table
        if (!Schema::hasTable('meeting_note_team_member')) {
            Schema::create('meeting_note_team_member', function (Blueprint $table) {
                $table->id();
                $table->foreignId('meeting_note_id')->constrained('meeting_notes')->onDelete('cascade');
                $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 3. Create task_team_member pivot table
        if (!Schema::hasTable('task_team_member')) {
            Schema::create('task_team_member', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
                $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_team_member');
        Schema::dropIfExists('meeting_note_team_member');
        
        if (Schema::hasColumn('meeting_notes', 'meeting_time')) {
            Schema::table('meeting_notes', function (Blueprint $table) {
                $table->dropColumn('meeting_time');
            });
        }
    }
};
