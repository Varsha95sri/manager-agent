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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->unsignedBigInteger('dependency_id')->nullable();
            $table->decimal('effort_estimation', 8, 2)->nullable();
            $table->decimal('actual_time', 8, 2)->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreign('dependency_id')->references('id')->on('tasks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['dependency_id']);
            $table->dropColumn(['priority', 'dependency_id', 'effort_estimation', 'actual_time', 'completed_at']);
        });
    }
};
