<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
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
            $table->string('country')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            
            // Team
            $table->text('founders')->nullable();
            $table->integer('team_size')->nullable();
            
            // Progress
            $table->decimal('funding_raised', 15, 2)->nullable();
            $table->integer('patients_impacted')->nullable();
            $table->integer('countries_reached')->nullable();
            
            // Media
            $table->text('logo')->nullable();
            $table->text('pitch_video')->nullable();
            $table->text('demo_video')->nullable();
            $table->json('images')->nullable();
            
            // Engagement
            $table->integer('votes_count')->default(0);
            $table->boolean('featured')->default(false);
            $table->enum('status', ['active', 'graduated', 'alumni'])->default('active');
            
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventures');
    }
};
