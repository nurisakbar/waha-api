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
        Schema::table('quota_purchases', function (Blueprint $table) {
            $table->string('xendit_invoice_id')->nullable()->after('payment_reference')->comment('Xendit invoice ID');
            $table->string('xendit_invoice_url', 500)->nullable()->after('xendit_invoice_id')->comment('Xendit payment URL');
            $table->index('xendit_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quota_purchases', function (Blueprint $table) {
            $table->dropIndex(['xendit_invoice_id']);
            $table->dropColumn(['xendit_invoice_id', 'xendit_invoice_url']);
        });
    }
};
