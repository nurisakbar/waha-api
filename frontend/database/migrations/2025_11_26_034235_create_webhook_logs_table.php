<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('webhook_id');
            $table->string('event_type', 100);
            $table->json('payload');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('triggered_at');
            $table->timestamps();
            
            $table->index('webhook_id');
            $table->index('event_type');
            $table->index('triggered_at');
            
            $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
