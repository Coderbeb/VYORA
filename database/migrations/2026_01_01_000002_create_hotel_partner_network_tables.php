<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pivot Table for Many-to-Many Hotel <-> Restaurant Partnerships
        Schema::create('hotel_partner_restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('hotel_id');
            $table->uuid('restaurant_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('hotel_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('owners')->onDelete('cascade');
            $table->unique(['hotel_id', 'restaurant_id']);
        });

        // 2. Add hotel_id and hotel_name to orders table for crystal-clear origin tracking
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('hotel_id')->nullable()->after('room_number');
            $table->string('hotel_name', 255)->nullable()->after('hotel_id');

            $table->foreign('hotel_id')->references('id')->on('owners')->onDelete('set null');
            $table->index('hotel_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
            $table->dropColumn(['hotel_id', 'hotel_name']);
        });

        Schema::dropIfExists('hotel_partner_restaurants');
    }
};
