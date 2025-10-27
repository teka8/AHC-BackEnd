<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('others')) {
            Schema::create('others', function (Blueprint $table) {
                $table->id();

                // Basic resource information
                $table->string('title');
                $table->string('creator');
                $table->text('description');
                $table->text('learning_objectives')->nullable();

                // Resource type and format
                $table->enum('resource_type', [
                    'Newsletter',
                    'Presentation',
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
        }

        // Create others resource categories table
        if (!Schema::hasTable('others_categories')) {
            Schema::create('others_categories', function (Blueprint $table) {
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
        }

        // Create others tags table
        if (!Schema::hasTable('others_tags')) {
            Schema::create('others_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('color')->default('#6b7280');
                $table->timestamps();
            });
        }

        // Pivot table for others resources and tags
        if (!Schema::hasTable('others_tag')) {
            Schema::create('others_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('others_id')->constrained()->onDelete('cascade');
                $table->foreignId('others_tag_id')->constrained()->onDelete('cascade');
                $table->timestamps();

                $table->unique(['others_id', 'others_tag_id'], 'others_tag_unique');
            });
        }

        // Create others resource access logs table
        if (!Schema::hasTable('others_access_logs')) {
            Schema::create('others_access_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('others_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('action'); // view, complete, download, interact
                $table->integer('time_spent_seconds')->default(0); // For tracking engagement
                $table->decimal('completion_percentage', 5, 2)->default(0); // For progress tracking
                $table->text('referrer')->nullable();
                $table->timestamps();

                $table->index(['others_id', 'action'], 'others_action');
                $table->index(['user_id', 'action'], 'others_user_action');
                $table->index('created_at', 'others_log_created_at');
            });
        }

        // Insert default educational categories
        $this->seedDefaultEducationalCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('others_access_logs');
        Schema::dropIfExists('others_tag');
        Schema::dropIfExists('others_tags');
        Schema::dropIfExists('others_categories');
        Schema::dropIfExists('others');
    }

    /**
     * Seed default others categories
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
            DB::table('others_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Insert some common others tags
        $tags = [
            ['name' => 'Newsletter', 'slug' => 'newsletter', 'color' => '#3b82f6'],
            ['name' => 'Presentation', 'slug' => 'presentation', 'color' => '#ef4444'],
        ];

        foreach ($tags as $tag) {
            DB::table('others_tags')->insertOrIgnore($tag);
        }
    }
};