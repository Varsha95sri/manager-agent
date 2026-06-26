<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('level')->nullable(); // e.g. L1, L2, Lead, Manager
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
