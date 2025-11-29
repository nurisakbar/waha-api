<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->foreignId('api_key_id')->nullable()->constrained()->onDelete('set null');
            $table->string('endpoint', 255);
            $table->string('method', 10);
            $table->string('ip_address', 45)->nullable();
            $table->integer('status_code');
            $table->integer('response_time')->comment('in milliseconds');
            $table->integer('request_size')->nullable();
            $table->integer('response_size')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('api_key_id');
            $table->index('endpoint');
            $table->index('status_code');
            $table->index('created_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};
