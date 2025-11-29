<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_statistics', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->date('date');
            $table->integer('messages_sent')->default(0);
            $table->integer('messages_received')->default(0);
            $table->integer('api_calls')->default(0);
            $table->integer('webhook_calls')->default(0);
            $table->bigInteger('storage_used')->default(0)->comment('in bytes');
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index('user_id');
            $table->index('date');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_statistics');
    }
};
