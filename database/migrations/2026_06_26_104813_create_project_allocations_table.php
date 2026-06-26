<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained()->cascadeOnDelete();
            $table->date('allocated_from');
            $table->date('allocated_to')->nullable();
            $table->string('role_on_project')->nullable();
            $table->string('status')->default('active'); // active, rolled_off
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_allocations');
    }
};
