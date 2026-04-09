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
        Schema::create('newsletter_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('others_id')->constrained('others')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('volume')->nullable();
            $table->string('issue_number')->nullable();
            $table->string('image_path')->nullable();
            $table->longText('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('others_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_articles');
    }
};
