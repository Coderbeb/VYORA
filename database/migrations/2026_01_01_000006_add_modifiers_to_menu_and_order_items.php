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
                $table->json('modifiers')->nullable();
            });
        }

        if (Schema::hasTable('order_items')) {
            if (!Schema::hasColumn('order_items', 'selected_variant')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('selected_variant')->nullable();
                });
            }
            if (!Schema::hasColumn('order_items', 'selected_addons')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->json('selected_addons')->nullable();
                });
            }
            if (!Schema::hasColumn('order_items', 'notes')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->string('notes')->nullable();
                });
            }
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
            $columns = ['selected_variant', 'selected_addons', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    Schema::table('order_items', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }
};
