<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->date('date_of_birth');
            $table->string('nationality');
            $table->string('country_of_residence');
            $table->text('address')->nullable();
            
            // Academic Background
            $table->enum('current_education_level', ['high-school', 'undergraduate', 'graduate', 'postgraduate', 'other']);
            $table->string('institution_name');
            $table->string('field_of_study');
            $table->string('gpa')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->text('academic_achievements')->nullable();
            
            // Research/Concept
            $table->string('research_area')->nullable();
            $table->text('concept_note')->nullable();
            $table->text('research_proposal')->nullable();
            
            // Motivation
            $table->text('motivation_letter');
            $table->text('career_goals');
            $table->text('why_this_scholarship');
            
            // Financial Need
            $table->text('financial_need_description')->nullable();
            $table->text('current_funding_sources')->nullable();
            
            // References
            $table->string('reference_1_name')->nullable();
            $table->string('reference_1_email')->nullable();
            $table->string('reference_1_relationship')->nullable();
            $table->string('reference_2_name')->nullable();
            $table->string('reference_2_email')->nullable();
            $table->string('reference_2_relationship')->nullable();
            
            // Documents
            $table->text('cv')->nullable();
            $table->text('transcript')->nullable();
            $table->text('motivation_letter_file')->nullable();
            $table->text('recommendation_letter_1')->nullable();
            $table->text('recommendation_letter_2')->nullable();
            $table->text('id_document')->nullable();
            $table->text('proof_of_enrollment')->nullable();
            
            // Additional
            $table->text('additional_info')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'under-review', 'shortlisted', 'interviewed', 'accepted', 'rejected', 'withdrawn'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('decision_at')->nullable();
            
            $table->timestamps();
            

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};
