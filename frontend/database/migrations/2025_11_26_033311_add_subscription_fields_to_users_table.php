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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('role', ['super_admin', 'admin', 'user'])->default('user')->after('avatar');
            $table->foreignId('subscription_plan_id')->nullable()->after('role')->constrained('plans')->onDelete('set null');
            $table->enum('subscription_status', ['active', 'cancelled', 'expired', 'trial'])->default('trial')->after('subscription_plan_id');
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->timestamp('last_login_at')->nullable()->after('trial_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn([
                'phone',
                'avatar',
                'role',
                'subscription_plan_id',
                'subscription_status',
                'trial_ends_at',
                'last_login_at',
            ]);
        });
    }
};
