<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->string('name', 255);
            $table->string('username', 100);
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('password_hash', 255)->nullable();
            $table->string('pin', 10)->nullable();
            $table->string('role', 50)->default('WAITER'); // FRONT_DESK, HOUSEKEEPING, CHEF, WAITER, CASHIER, MANAGER
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->unique(['owner_id', 'username']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};
