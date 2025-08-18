<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to purchases table
        Schema::table('purchases', function (Blueprint $table) {
            if (!$this->indexExists('purchases', 'purchases_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!$this->indexExists('purchases', 'purchases_member_id_created_at_index')) {
                $table->index(['member_id', 'created_at']);
            }
            if (!$this->indexExists('purchases', 'purchases_product_id_created_at_index')) {
                $table->index(['product_id', 'created_at']);
            }
            if (!$this->indexExists('purchases', 'purchases_order_id_index')) {
                $table->index('order_id');
            }
        });

        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'products_game_id_is_active_index')) {
                $table->index(['game_id', 'is_active']);
            }
            if (!$this->indexExists('products', 'products_is_active_price_tamu_index')) {
                $table->index(['is_active', 'price_tamu']);
            }
            if (!$this->indexExists('products', 'products_is_active_price_member_index')) {
                $table->index(['is_active', 'price_member']);
            }
            if (!$this->indexExists('products', 'products_sku_index')) {
                $table->index('sku');
            }
        });

        // Add indexes to games table
        Schema::table('games', function (Blueprint $table) {
            if (!$this->indexExists('games', 'games_is_active_order_index')) {
                $table->index(['is_active', 'order']);
            }
            if (!$this->indexExists('games', 'games_slug_index')) {
                $table->index('slug');
            }
        });

        // Add indexes to categories table
        Schema::table('categories', function (Blueprint $table) {
            if (!$this->indexExists('categories', 'categories_is_active_order_index')) {
                $table->index(['is_active', 'order']);
            }
        });

        // Add indexes to members table
        Schema::table('members', function (Blueprint $table) {
            if (!$this->indexExists('members', 'members_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!$this->indexExists('members', 'members_phone_index')) {
                $table->index('phone');
            }
            if (!$this->indexExists('members', 'members_email_index')) {
                $table->index('email');
            }
            if (!$this->indexExists('members', 'members_username_index')) {
                $table->index('username');
            }
        });

        // Add indexes to topups table
        Schema::table('topups', function (Blueprint $table) {
            if (!$this->indexExists('topups', 'topups_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!$this->indexExists('topups', 'topups_member_id_created_at_index')) {
                $table->index(['member_id', 'created_at']);
            }
            if (!$this->indexExists('topups', 'topups_topup_id_index')) {
                $table->index('topup_id');
            }
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_status_created_at_index')) {
                $table->index(['status', 'created_at']);
            }
            if (!$this->indexExists('users', 'users_username_index')) {
                $table->index('username');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndexIfExists(['status', 'created_at']);
            $table->dropIndexIfExists(['member_id', 'created_at']);
            $table->dropIndexIfExists(['product_id', 'created_at']);
            $table->dropIndexIfExists(['order_id']);
        });

        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndexIfExists(['game_id', 'is_active']);
            $table->dropIndexIfExists(['is_active', 'price_tamu']);
            $table->dropIndexIfExists(['is_active', 'price_member']);
            $table->dropIndexIfExists(['sku']);
        });

        // Remove indexes from games table
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndexIfExists(['is_active', 'order']);
            $table->dropIndexIfExists(['slug']);
        });

        // Remove indexes from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndexIfExists(['is_active', 'order']);
        });

        // Remove indexes from members table
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndexIfExists(['status', 'created_at']);
            $table->dropIndexIfExists(['phone']);
            $table->dropIndexIfExists(['email']);
            $table->dropIndexIfExists(['username']);
        });

        // Remove indexes from topups table
        Schema::table('topups', function (Blueprint $table) {
            $table->dropIndexIfExists(['status', 'created_at']);
            $table->dropIndexIfExists(['member_id', 'created_at']);
            $table->dropIndexIfExists(['topup_id']);
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists(['status', 'created_at']);
            $table->dropIndexIfExists(['username']);
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }
};
