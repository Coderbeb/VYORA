<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_items') && !Schema::hasColumn('menu_items', 'modifiers')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->json('modifiers')->nullable()->after('is_veg');
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'selected_variant')) {
                    $table->string('selected_variant')->nullable()->after('price');
                }
                if (!Schema::hasColumn('order_items', 'selected_addons')) {
                    $table->json('selected_addons')->nullable()->after('selected_variant');
                }
                if (!Schema::hasColumn('order_items', 'notes')) {
                    $table->string('notes')->nullable()->after('selected_addons');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('menu_items') && Schema::hasColumn('menu_items', 'modifiers')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('modifiers');
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $columns = ['selected_variant', 'selected_addons', 'notes'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('order_items', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
