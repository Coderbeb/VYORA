<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tier', 50);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('max_tables')->default(5);
            $table->json('features')->nullable();
            $table->timestamps();
        });

        // 2. Owners (Restaurant Tenants & SuperAdmin)
        Schema::create('owners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 255)->unique();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('restaurant_name', 255)->nullable();
            $table->string('owner_name', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('role', 50)->default('RESTAURANT_OWNER');
            $table->uuid('plan_id')->nullable();
            $table->string('subscription_status', 50)->default('active');
            $table->boolean('show_on_landing')->default(false);
            $table->string('cuisine', 100)->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
        });

        // 3. Tables
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->integer('table_number');
            $table->text('qr_code_data');
            $table->longText('qr_code_image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->unique(['owner_id', 'table_number']);
            $table->index('table_number');
        });

        // 4. Menu Categories
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->index('owner_id');
        });

        // 5. Menu Items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->uuid('category_id')->nullable();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->longText('image_url')->nullable();
            $table->integer('preparation_time')->default(15);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('menu_categories')->onDelete('set null');
            $table->index('is_available');
            $table->index('category_id');
        });

        // 6. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->integer('table_number');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->integer('estimated_time')->default(15);
            $table->string('status', 50)->default('pending');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->index('table_number');
            $table->index('status');
            $table->index('created_at');
        });

        // 7. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('menu_item_id');
            $table->string('menu_item_name', 255);
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->index('order_id');
            $table->index('menu_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('owners');
        Schema::dropIfExists('subscription_plans');
    }
};
