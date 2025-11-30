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
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('session_name');
            $table->uuid('session_id')->unique()->comment('WAHA session ID (UUID)');
            $table->enum('status', ['pairing', 'connected', 'disconnected', 'failed'])->default('pairing');
            $table->text('qr_code')->nullable();
            $table->timestamp('qr_code_expires_at')->nullable();
            $table->json('device_info')->nullable();
            $table->string('waha_instance_url')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('disconnected_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
