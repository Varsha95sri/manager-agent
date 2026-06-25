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
        Schema::table('performance_reports', function (Blueprint $table) {
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'executive'])->default('daily')->after('id');
            $table->json('workload_analysis')->nullable()->after('risks');
            $table->json('recommendations')->nullable()->after('workload_analysis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_reports', function (Blueprint $table) {
            $table->dropColumn(['report_type', 'workload_analysis', 'recommendations']);
        });
    }
};
