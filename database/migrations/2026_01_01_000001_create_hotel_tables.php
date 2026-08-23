<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update subscription_plans table
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('business_type', 50)->default('RESTAURANT')->after('tier'); // RESTAURANT, HOTEL, COMBO
            $table->integer('max_rooms')->default(0)->after('max_tables');
            $table->unique(['tier', 'business_type']);
        });

        // 2. Update owners table
        Schema::table('owners', function (Blueprint $table) {
            $table->string('business_type', 50)->default('RESTAURANT')->after('role'); // RESTAURANT, HOTEL, BOTH
            $table->uuid('partner_restaurant_id')->nullable()->after('plan_id');

            $table->foreign('partner_restaurant_id')->references('id')->on('owners')->onDelete('set null');
        });

        // 3. Room Categories (Deluxe, Suite, Standard, Executive)
        Schema::create('room_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->string('name', 100);
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->integer('max_occupancy')->default(2);
            $table->json('amenities')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->index('owner_id');
        });

        // 4. Hotel Rooms
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->uuid('category_id')->nullable();
            $table->string('room_number', 50);
            $table->integer('floor')->default(1);
            $table->string('status', 50)->default('vacant_clean'); // vacant_clean, vacant_dirty, occupied, maintenance
            $table->text('qr_code_data')->nullable();
            $table->longText('qr_code_image_url')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('room_categories')->onDelete('set null');
            $table->unique(['owner_id', 'room_number']);
            $table->index('status');
        });

        // 5. Hotel Guests Directory
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->string('full_name', 255);
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('id_type', 100)->nullable(); // Passport, Driving License, National ID
            $table->string('id_number', 100)->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->index('owner_id');
            $table->index('phone');
        });

        // 6. Hotel Bookings & Stays
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->uuid('room_id');
            $table->uuid('guest_id');
            $table->timestamp('check_in');
            $table->timestamp('expected_check_out')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->decimal('room_rate', 10, 2)->default(0.00);
            $table->decimal('advance_deposit', 10, 2)->default(0.00);
            $table->string('status', 50)->default('active'); // active, checked_out, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');
            $table->index('status');
        });

        // 7. Hotel Service & Housekeeping Requests
        Schema::create('hotel_service_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->uuid('room_id');
            $table->uuid('booking_id')->nullable();
            $table->string('service_name', 255); // Room Cleaning, Extra Towels, Packaged Water, Toiletries, Extra Bed, Wakeup Call, Luggage Assistance, Taxi Call
            $table->string('category', 50)->default('housekeeping'); // housekeeping, concierge, maintenance
            $table->string('status', 50)->default('pending'); // pending, in_progress, completed
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->index('status');
            $table->index('created_at');
        });

        // 8. Update orders table with hotel room service capabilities
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('room_id')->nullable()->after('table_number');
            $table->string('room_number', 50)->nullable()->after('room_id');
            $table->string('order_type', 50)->default('dine_in')->after('room_number'); // dine_in, room_service
            $table->uuid('booking_id')->nullable()->after('order_type');
            $table->string('billing_type', 50)->default('folio')->after('booking_id'); // folio, direct_restaurant
            $table->uuid('fulfilled_by_id')->nullable()->after('billing_type'); // if fulfilled by linked partner restaurant

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            $table->foreign('fulfilled_by_id')->references('id')->on('owners')->onDelete('set null');
            $table->index('order_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['fulfilled_by_id']);
            $table->dropColumn(['room_id', 'room_number', 'order_type', 'booking_id', 'billing_type', 'fulfilled_by_id']);
        });

        Schema::dropIfExists('hotel_service_requests');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_categories');

        Schema::table('owners', function (Blueprint $table) {
            $table->dropForeign(['partner_restaurant_id']);
            $table->dropColumn(['business_type', 'partner_restaurant_id']);
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'max_rooms']);
        });
    }
};
