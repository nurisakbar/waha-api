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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->unique()->nullable()->after('role')->comment('Kode referral unik untuk user');
            $table->uuid('referred_by')->nullable()->after('referral_code')->comment('ID user yang mereferensikan');
            $table->index('referral_code');
            $table->index('referred_by');
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropIndex(['referred_by']);
            $table->dropIndex(['referral_code']);
            $table->dropColumn(['referral_code', 'referred_by']);
        });
    }
};
