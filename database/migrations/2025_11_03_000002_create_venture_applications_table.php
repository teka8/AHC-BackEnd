<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venture_applications', function (Blueprint $table) {
            $table->id();
            
            // Venture Info
            $table->string('venture_name');
            $table->string('tagline')->nullable();
            $table->text('description');
            $table->enum('focus_area', [
                'mental-health',
                'telemedicine',
                'pharmaceuticals',
                'biotech',
                'medtech',
                'diagnostics',
                'health-tech',
                'other'
            ]);
            $table->enum('stage', ['idea', 'prototype', 'early-stage', 'growth', 'scale']);
            $table->integer('founded_year')->nullable();
            $table->string('country');
            $table->string('website')->nullable();
            
            // Contact Info
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            
            // Team
            $table->text('founders');
            $table->integer('team_size')->nullable();
            $table->text('team_description')->nullable();
            
            // Problem & Solution
            $table->text('problem_statement');
            $table->text('solution_description');
            $table->text('target_market');
            $table->text('unique_value_proposition');
            
            // Traction
            $table->text('current_stage_description');
            $table->integer('patients_served')->nullable();
            $table->decimal('revenue_generated', 15, 2)->nullable();
            $table->decimal('funding_raised', 15, 2)->nullable();
            $table->text('key_milestones')->nullable();
            
            // Funding
            $table->decimal('funding_sought', 15, 2)->nullable();
            $table->text('use_of_funds')->nullable();
            
            // Documents
            $table->text('pitch_deck')->nullable();
            $table->text('business_plan')->nullable();
            $table->text('financial_projections')->nullable();
            
            // Additional
            $table->text('why_apply');
            $table->text('additional_info')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'under-review', 'accepted', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venture_applications');
    }
};
