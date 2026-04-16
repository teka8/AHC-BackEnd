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
        Schema::table('others', function (Blueprint $table) {
            $table->string('newsletter_volume')->nullable();
            $table->string('newsletter_issue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('others', function (Blueprint $table) {
            $table->dropColumn(['newsletter_volume', 'newsletter_issue']);
        });
    }
};
