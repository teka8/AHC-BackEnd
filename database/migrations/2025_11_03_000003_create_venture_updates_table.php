<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venture_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venture_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('update_type', ['milestone', 'funding', 'partnership', 'product', 'team', 'general'])->default('general');
            $table->json('media')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venture_updates');
    }
};
