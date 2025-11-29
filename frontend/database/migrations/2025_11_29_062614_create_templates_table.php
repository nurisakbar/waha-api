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
        Schema::create('templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name', 255);
            $table->text('content');
            $table->enum('message_type', ['text', 'image', 'video', 'document', 'button', 'list'])->default('text');
            $table->json('variables')->nullable(); // Array of variable names like ["name", "order_id", "amount"]
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // For storing additional data like image URL, buttons, etc.
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_active');
            $table->index(['user_id', 'is_active']);
            $table->index('created_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
