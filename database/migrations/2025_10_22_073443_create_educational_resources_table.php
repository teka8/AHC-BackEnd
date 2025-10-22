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
        Schema::create('educational_resources', function (Blueprint $table) {
            $table->id();
            
            // Basic resource information
            $table->string('title');
            $table->string('creator');
            $table->text('description');
            $table->text('learning_objectives')->nullable();
            
            // Resource type and format
            $table->enum('resource_type', [
                'Video',
                'Podcast',
                'Interactive Module',
                'Lesson Plan',
                'Teaching Guide',
                'Presentation',
                'Case Study',
                'Simulation',
                'Other'
            ]);
            
            // Educational metadata
            $table->enum('educational_level', [
                'Undergraduate',
                'Postgraduate',
                'Faculty Development',
                'Continuing Education',
                'All Levels'
            ])->default('All Levels');
            
            $table->string('subject_area');
            $table->integer('duration_minutes')->nullable(); // For time-based resources
            $table->string('language')->default('English');
            
            // File and media information
            $table->string('file_path')->nullable(); // For downloadable resources
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('embed_code')->nullable(); // For videos, interactive content
            $table->string('thumbnail_path')->nullable();
            
            // Categorization
            $table->json('tags')->nullable();
            $table->string('difficulty_level')->nullable(); // Beginner, Intermediate, Advanced
            
            // Access and visibility
            $table->boolean('is_featured')->default(false);
            $table->enum('access_level', ['public', 'partner_only', 'internal_only'])->default('public');
            $table->boolean('requires_enrollment')->default(false);
            
            // Workflow and status
            $table->enum('status', ['draft', 'under_review', 'approved', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            
            // Ownership
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Analytics
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('completion_count')->default(0);
            $table->unsignedBigInteger('download_count')->default(0);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance (with shorter names)
            $table->index(['resource_type', 'status'], 'edu_res_type_status');
            $table->index(['educational_level', 'status'], 'edu_level_status');
            $table->index(['is_featured', 'status'], 'edu_featured_status');
            $table->index('access_level', 'edu_access_level');
            $table->index('created_by', 'edu_created_by');
            
            // Fulltext index with shorter name
            $table->fulltext(['title', 'description', 'learning_objectives'], 'edu_res_fulltext');
        });

        // Create educational resource categories table
        Schema::create('educational_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#10b981');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('is_active', 'edu_cat_active');
            $table->index('sort_order', 'edu_cat_order');
        });

        // Create educational resource tags table
        Schema::create('educational_resource_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#6b7280');
            $table->timestamps();
        });

        // Pivot table for educational resources and tags
        Schema::create('educational_resource_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('educational_resource_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['educational_resource_id', 'educational_resource_tag_id'], 'edu_res_tag_unique');
        });

        // Create resource access logs table
        Schema::create('educational_resource_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('action'); // view, complete, download, interact
            $table->integer('time_spent_seconds')->default(0); // For tracking engagement
            $table->decimal('completion_percentage', 5, 2)->default(0); // For progress tracking
            $table->text('referrer')->nullable();
            $table->timestamps();
            
            $table->index(['educational_resource_id', 'action'], 'edu_log_res_action');
            $table->index(['user_id', 'action'], 'edu_log_user_action');
            $table->index('created_at', 'edu_log_created_at');
        });

        // Insert default educational categories
        $this->seedDefaultEducationalCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_resource_access_logs');
        Schema::dropIfExists('educational_resource_tag');
        Schema::dropIfExists('educational_resource_tags');
        Schema::dropIfExists('educational_categories');
        Schema::dropIfExists('educational_resources');
    }

    /**
     * Seed default educational categories
     */
    private function seedDefaultEducationalCategories(): void
    {
        $categories = [
            ['name' => 'Anatomy', 'slug' => 'anatomy', 'color' => '#ef4444', 'sort_order' => 10],
            ['name' => 'Physiology', 'slug' => 'physiology', 'color' => '#3b82f6', 'sort_order' => 20],
            ['name' => 'Pharmacology', 'slug' => 'pharmacology', 'color' => '#8b5cf6', 'sort_order' => 30],
            ['name' => 'Pathology', 'slug' => 'pathology', 'color' => '#ec4899', 'sort_order' => 40],
            ['name' => 'Clinical Skills', 'slug' => 'clinical-skills', 'color' => '#10b981', 'sort_order' => 50],
            ['name' => 'Medical Ethics', 'slug' => 'medical-ethics', 'color' => '#f59e0b', 'sort_order' => 60],
            ['name' => 'Public Health', 'slug' => 'public-health', 'color' => '#06b6d4', 'sort_order' => 70],
            ['name' => 'Research Methods', 'slug' => 'research-methods', 'color' => '#84cc16', 'sort_order' => 80],
            ['name' => 'Teaching Methodology', 'slug' => 'teaching-methodology', 'color' => '#f97316', 'sort_order' => 90],
            ['name' => 'Other', 'slug' => 'other-educational', 'color' => '#6b7280', 'sort_order' => 100],
        ];

        foreach ($categories as $category) {
            DB::table('educational_categories')->insert($category);
        }

        // Insert some common educational tags
        $tags = [
            ['name' => 'Interactive', 'slug' => 'interactive', 'color' => '#3b82f6'],
            ['name' => 'Video Lecture', 'slug' => 'video-lecture', 'color' => '#ef4444'],
            ['name' => 'Case Study', 'slug' => 'case-study', 'color' => '#10b981'],
            ['name' => 'Simulation', 'slug' => 'simulation', 'color' => '#8b5cf6'],
            ['name' => 'Assessment', 'slug' => 'assessment', 'color' => '#f59e0b'],
            ['name' => 'Beginner', 'slug' => 'beginner', 'color' => '#84cc16'],
            ['name' => 'Advanced', 'slug' => 'advanced', 'color' => '#ec4899'],
            ['name' => 'Clinical', 'slug' => 'clinical', 'color' => '#06b6d4'],
        ];

        foreach ($tags as $tag) {
            DB::table('educational_resource_tags')->insert($tag);
        }
    }
};