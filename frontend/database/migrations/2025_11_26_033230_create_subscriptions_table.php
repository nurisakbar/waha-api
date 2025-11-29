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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->foreignId('plan_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['active', 'cancelled', 'expired', 'past_due'])->default('active');
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('plan_id');
            $table->index('status');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
