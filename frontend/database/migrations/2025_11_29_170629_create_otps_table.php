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
        Schema::create('otps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('template_id')->nullable();
            $table->string('phone_number', 20);
            $table->string('code', 6)->comment('Kode OTP 6 digit');
            $table->enum('status', ['pending', 'verified', 'expired', 'failed'])->default('pending');
            $table->integer('attempts')->default(0)->comment('Jumlah percobaan verifikasi');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->string('device_id')->nullable()->comment('Device ID yang digunakan untuk mengirim OTP');
            $table->text('message_id')->nullable()->comment('ID pesan WhatsApp yang berisi OTP');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('phone_number');
            $table->index('code');
            $table->index('status');
            $table->index('expires_at');
            $table->index(['phone_number', 'status']);
            $table->index(['user_id', 'status']);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
