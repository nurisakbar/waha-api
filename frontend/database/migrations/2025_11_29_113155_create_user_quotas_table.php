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
        Schema::create('user_quotas', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->decimal('balance', 15, 2)->default(0)->comment('Balance dalam IDR untuk membayar pesan');
            $table->integer('text_quota')->default(0)->comment('Quota khusus untuk pesan text (optional)');
            $table->integer('multimedia_quota')->default(0)->comment('Quota khusus untuk pesan multimedia (optional)');
            $table->timestamps();
            
            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quotas');
    }
};
