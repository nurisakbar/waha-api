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
        Schema::create('message_pricing_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('text_with_watermark_price', 10, 2)->default(0)->comment('Harga pesan text dengan watermark (gratis = 0)');
            $table->decimal('text_without_watermark_price', 10, 2)->default(0)->comment('Harga pesan text tanpa watermark');
            $table->decimal('multimedia_price', 10, 2)->default(0)->comment('Harga pesan multimedia (image, document, dll)');
            $table->string('watermark_text', 255)->default('Sent via WAHA SaaS')->comment('Text watermark yang akan ditambahkan');
            $table->boolean('is_active')->default(true)->comment('Apakah pricing ini aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_pricing_settings');
    }
};
