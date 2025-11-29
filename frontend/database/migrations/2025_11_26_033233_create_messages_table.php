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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('session_id');
            $table->string('whatsapp_message_id')->nullable();
            $table->string('from_number', 20);
            $table->string('to_number', 20);
            $table->enum('message_type', ['text', 'image', 'video', 'audio', 'document', 'location', 'contact', 'sticker', 'voice'])->default('text');
            $table->text('content')->nullable();
            $table->string('media_url', 500)->nullable();
            $table->string('media_mime_type', 100)->nullable();
            $table->bigInteger('media_size')->nullable();
            $table->text('caption')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('from_number');
            $table->index('to_number');
            $table->index('whatsapp_message_id');
            $table->index('direction');
            $table->index('status');
            $table->index('created_at');
            $table->index(['user_id', 'session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('whatsapp_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
