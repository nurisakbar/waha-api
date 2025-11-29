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
        Schema::create('quota_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('purchase_number', 50)->unique()->comment('Nomor pembelian (invoice-like)');
            $table->decimal('amount', 15, 2)->comment('Jumlah yang dibayar (IDR)');
            $table->decimal('balance_added', 15, 2)->comment('Balance yang ditambahkan (IDR)');
            $table->integer('text_quota_added')->default(0)->comment('Text quota yang ditambahkan (optional)');
            $table->integer('multimedia_quota_added')->default(0)->comment('Multimedia quota yang ditambahkan (optional)');
            $table->enum('payment_method', ['manual', 'bank_transfer', 'credit_card', 'e_wallet', 'other'])->default('manual');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_reference')->nullable()->comment('Referensi pembayaran (no rekening, dll)');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('status');
            $table->index('purchase_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quota_purchases');
    }
};
