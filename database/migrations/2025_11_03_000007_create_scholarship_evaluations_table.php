<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('scholarship_applications')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            
            // Scoring (0-10 scale)
            $table->decimal('academic_performance_score', 3, 1);
            $table->decimal('motivation_score', 3, 1);
            $table->decimal('research_quality_score', 3, 1);
            $table->decimal('financial_need_score', 3, 1);
            $table->decimal('overall_score', 3, 1);
            
            // Comments
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->enum('recommendation', ['strong-accept', 'accept', 'waitlist', 'reject']);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_evaluations');
    }
};
