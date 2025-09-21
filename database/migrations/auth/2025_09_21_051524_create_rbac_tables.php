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
        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 100)->unique();
            $table->timestamps();
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission_name', 100)->unique();
            $table->timestamps();
        });

        // Pivot: role_permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles', 'id')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions', 'id')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // Pivot: user_roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles', 'id')->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
