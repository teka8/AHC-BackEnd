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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            
            // Basic document information
            $table->string('title');
            $table->string('author');
            $table->date('publication_date');
            $table->text('abstract');
            
            // File information
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->string('file_extension');
            $table->string('mime_type')->nullable();
            
            // Categorization
            $table->enum('document_type', [
                'Policy Brief',
                'Research Paper',
                'Annual Report',
                'Quarterly Report',
                'Assessment Report',
                'AHC Guideline',
                'Educational Material',
                'Newsletter',
                'Other'
            ]);
            $table->string('category');
            $table->json('tags')->nullable(); // Store tags as JSON array
            
            // Version control
            $table->string('version')->default('1.0');
            $table->foreignId('previous_version_id')->nullable()->constrained('documents')->onDelete('set null');
            
            // Featured and visibility
            $table->boolean('is_featured')->default(false);
            $table->enum('access_level', ['public', 'partner_only', 'internal_only'])->default('public');
            
            // Workflow and status
            $table->enum('status', ['draft', 'under_review', 'approved', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            
            // Ownership
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Analytics
            $table->unsignedBigInteger('download_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['document_type', 'status']);
            $table->index(['category', 'status']);
            $table->index(['publication_date', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('access_level');
            $table->index('created_by');
            $table->fulltext(['title', 'abstract']); // For search functionality
        });

        // Create document categories table for better normalization
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#3b82f6'); // For UI purposes
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('sort_order');
        });

        // Create document tags table for many-to-many relationship
        Schema::create('document_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#6b7280');
            $table->timestamps();
        });

        // Pivot table for documents and tags
        Schema::create('document_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['document_id', 'document_tag_id']);
        });

        // Create document access logs table
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('action'); // view, download, preview
            $table->text('referrer')->nullable();
            $table->timestamps();
            
            $table->index(['document_id', 'action']);
            $table->index(['user_id', 'action']);
            $table->index('created_at');
        });

        // Insert default categories
        $this->seedDefaultCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
        Schema::dropIfExists('document_tag');
        Schema::dropIfExists('document_tags');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('documents');
    }

    /**
     * Seed default document categories
     */
    private function seedDefaultCategories(): void
    {
        $categories = [
            ['name' => 'Medical Education', 'slug' => 'medical-education', 'color' => '#3b82f6', 'sort_order' => 10],
            ['name' => 'Public Health', 'slug' => 'public-health', 'color' => '#ef4444', 'sort_order' => 20],
            ['name' => 'Curriculum Development', 'slug' => 'curriculum-development', 'color' => '#10b981', 'sort_order' => 30],
            ['name' => 'Student Assessment', 'slug' => 'student-assessment', 'color' => '#f59e0b', 'sort_order' => 40],
            ['name' => 'Faculty Development', 'slug' => 'faculty-development', 'color' => '#8b5cf6', 'sort_order' => 50],
            ['name' => 'Healthcare Policy', 'slug' => 'healthcare-policy', 'color' => '#ec4899', 'sort_order' => 60],
            ['name' => 'Research Methodology', 'slug' => 'research-methodology', 'color' => '#06b6d4', 'sort_order' => 70],
            ['name' => 'Other', 'slug' => 'other', 'color' => '#6b7280', 'sort_order' => 100],
        ];

        foreach ($categories as $category) {
            DB::table('document_categories')->insert($category);
        }

        // Insert some common tags
        $tags = [
            ['name' => 'Research', 'slug' => 'research', 'color' => '#3b82f6'],
            ['name' => 'Education', 'slug' => 'education', 'color' => '#10b981'],
            ['name' => 'Health', 'slug' => 'health', 'color' => '#ef4444'],
            ['name' => 'Policy', 'slug' => 'policy', 'color' => '#8b5cf6'],
            ['name' => 'Assessment', 'slug' => 'assessment', 'color' => '#f59e0b'],
            ['name' => 'Curriculum', 'slug' => 'curriculum', 'color' => '#ec4899'],
            ['name' => 'Guidelines', 'slug' => 'guidelines', 'color' => '#06b6d4'],
            ['name' => 'Best Practices', 'slug' => 'best-practices', 'color' => '#84cc16'],
        ];

        foreach ($tags as $tag) {
            DB::table('document_tags')->insert($tag);
        }
    }
};