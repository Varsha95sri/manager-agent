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
        Schema::create('gitlab_merge_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->string('gitlab_mr_id');
            $table->string('state')->default('opened'); // opened, closed, merged
            $table->string('title')->nullable();
            $table->timestamp('merged_at')->nullable();
            $table->timestamps();
            
            $table->unique(['project_id', 'gitlab_mr_id']);
        });

        Schema::create('gitlab_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->string('gitlab_issue_id');
            $table->string('state')->default('opened'); // opened, closed
            $table->string('title')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['project_id', 'gitlab_issue_id']);
        });

        Schema::create('project_code_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->integer('code_quality_score')->default(100);
            $table->integer('technical_debt_score')->default(0);
            $table->integer('security_score')->default(100);
            $table->integer('test_coverage_score')->default(0);
            $table->integer('bug_density_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_code_metrics');
        Schema::dropIfExists('gitlab_issues');
        Schema::dropIfExists('gitlab_merge_requests');
    }
};
