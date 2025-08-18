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
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('digiflazz_ref_id')->nullable()->after('tripay_reference');
            $table->string('digiflazz_status')->nullable()->after('digiflazz_ref_id');
            $table->text('digiflazz_message')->nullable()->after('digiflazz_status');
            $table->string('digiflazz_buyer_sku_code')->nullable()->after('digiflazz_message');
            $table->string('digiflazz_customer_no')->nullable()->after('digiflazz_buyer_sku_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'digiflazz_ref_id',
                'digiflazz_status',
                'digiflazz_message',
                'digiflazz_buyer_sku_code',
                'digiflazz_customer_no'
            ]);
        });
    }
};
