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
            $table->string('tripay_reference')->nullable()->after('payment_status');
            $table->string('tripay_payment_url')->nullable()->after('tripay_reference');
            $table->string('tripay_qr_code')->nullable()->after('tripay_payment_url');
            $table->string('tripay_merchant_ref')->nullable()->after('tripay_qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'tripay_reference',
                'tripay_payment_url',
                'tripay_qr_code',
                'tripay_merchant_ref'
            ]);
        });
    }
};
