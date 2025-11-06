<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venture_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venture_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->unique(['venture_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venture_votes');
    }
};
