<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_number', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('subscription_id');
            $table->index('invoice_number');
            $table->index('status');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
