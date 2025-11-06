<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('program_type', ['undergraduate', 'graduate', 'postgraduate', 'research', 'fellowship']);
            $table->text('eligibility_criteria');
            $table->json('required_documents');
            $table->json('benefits');
            $table->string('coverage');
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('deadline');
            $table->date('application_start_date')->nullable();
            $table->enum('status', ['upcoming', 'open', 'closed'])->default('upcoming');
            $table->integer('available_slots')->nullable();
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
