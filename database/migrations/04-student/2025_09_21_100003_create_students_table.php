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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('admission_number')->unique();
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('admission_date');
            $table->foreignId('current_standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('current_section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('current_academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->enum('status', ['active', 'alumni', 'left']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
