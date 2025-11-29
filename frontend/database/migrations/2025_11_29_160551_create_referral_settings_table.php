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
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('text_quota_bonus')->default(0)->comment('Bonus text quota yang diberikan untuk setiap referral');
            $table->integer('multimedia_quota_bonus')->default(0)->comment('Bonus multimedia quota yang diberikan untuk setiap referral');
            $table->boolean('is_active')->default(true)->comment('Apakah sistem referral aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_settings');
    }
};
