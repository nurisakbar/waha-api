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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('session_id')->nullable();
            $table->string('name');
            $table->string('url', 500);
            $table->json('events');
            $table->string('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('failure_count')->default(0);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('is_active');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('whatsapp_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
