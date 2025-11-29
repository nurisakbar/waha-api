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
        Schema::table('user_quotas', function (Blueprint $table) {
            $table->integer('free_text_quota')->default(0)->after('multimedia_quota')->comment('Quota gratis untuk pesan text dengan watermark (reset setiap tanggal 1)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quotas', function (Blueprint $table) {
            $table->dropColumn('free_text_quota');
        });
    }
};
