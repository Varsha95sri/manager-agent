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
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'archived'])->default('planning');
            $table->integer('progress_percent')->default(0);
            $table->date('deadline')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->nullable();
            $table->integer('health_score')->nullable();
            $table->string('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'progress_percent', 'deadline', 'risk_level', 'health_score', 'category']);
        });
    }
};
