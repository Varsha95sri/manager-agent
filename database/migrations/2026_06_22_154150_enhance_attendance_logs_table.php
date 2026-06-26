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
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->time('check_out')->nullable()->after('check_in');
            $table->string('leave_type')->nullable()->after('status');
        });

        if (config('database.default') === 'mysql') {
            // Use raw SQL to update the enum
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN status ENUM('present', 'absent', 'late', 'leave') NOT NULL");
        } elseif (config('database.default') === 'pgsql') {
            // For PostgreSQL, drop the check constraint instead of using MODIFY COLUMN
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendance_logs DROP CONSTRAINT IF EXISTS attendance_logs_status_check");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            // Revert enum back
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE attendance_logs MODIFY COLUMN status ENUM('present', 'absent', 'late') NOT NULL");
        } elseif (config('database.default') === 'pgsql') {
            // No easy reverse for Postgres check constraint drop here, so we do nothing
        }

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['check_out', 'leave_type']);
        });
    }
};
