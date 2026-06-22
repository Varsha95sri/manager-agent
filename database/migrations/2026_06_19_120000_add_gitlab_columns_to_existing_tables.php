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
        Schema::table('team_members', function (Blueprint $table) {
            $table->unsignedBigInteger('gitlab_user_id')->nullable()->after('github_id');
            $table->string('gitlab_username')->nullable()->after('gitlab_user_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('gitlab_project_id')->nullable()->after('description');
            $table->string('gitlab_repo_url')->nullable()->after('gitlab_project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['gitlab_project_id', 'gitlab_repo_url']);
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['gitlab_user_id', 'gitlab_username']);
        });
    }
};
