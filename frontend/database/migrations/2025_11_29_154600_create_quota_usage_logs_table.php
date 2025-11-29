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
        Schema::create('quota_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('message_id')->nullable();
            $table->enum('quota_type', ['text_quota', 'multimedia_quota', 'balance'])->comment('Jenis quota yang digunakan');
            $table->decimal('amount', 15, 2)->comment('Jumlah quota yang digunakan (untuk balance dalam IDR, untuk quota dalam jumlah pesan)');
            $table->string('message_type')->nullable()->comment('Jenis pesan (text, image, document, dll)');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('set null');
            $table->index('user_id');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quota_usage_logs');
    }
};
