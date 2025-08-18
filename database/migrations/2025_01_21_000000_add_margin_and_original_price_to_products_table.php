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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('original_price')->default(0)->after('price_member');
            $table->integer('margin_tamu')->default(0)->after('original_price');
            $table->integer('margin_member')->default(0)->after('margin_tamu');
            $table->boolean('auto_update_price')->default(false)->after('margin_member');
            $table->timestamp('last_price_update')->nullable()->after('auto_update_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'margin_tamu', 'margin_member', 'auto_update_price', 'last_price_update']);
        });
    }
};
