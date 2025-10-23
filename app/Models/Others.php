<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Others extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'creator',
        'description',
        'resource_type',
        'subject_area',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'mime_type',
        'tags',
        'is_featured',
        'access_level',
        'status',
        'published_at',
        'created_by',
        'updated_by',
        'approved_by',
        'download_count',
        'view_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    protected $attributes = [
        'is_featured' => false,
        'access_level' => 'public',
        'status' => 'draft',
        'download_count' => 0,
        'view_count' => 0,
    ];

    // Other Others Document type constants
    const TYPE_NEWSLETTER = 'Newsletter';
    const TYPE_PRESENTATION = 'Presentation';

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    // Access level constants
    const ACCESS_PUBLIC = 'public';
    const ACCESS_PARTNER_ONLY = 'partner_only';
    const ACCESS_INTERNAL_ONLY = 'internal_only';

    /**
     * Get the user who created the others document
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the others document
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the others document
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the previous version of this document
     */
    public function previousVersion(): BelongsTo
    {
        return $this->belongsTo(Others::class, 'previous_version_id');
    }

    /**
     * Get all tags associated with the document
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(OthersTag::class, 'others_tag');
    }

    /**
     * Get all access logs for this document
     */
    public function accessLogs()
    {
        return $this->hasMany(OthersAccessLog::class);
    }

    /**
     * Scope a query to only include published documents
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope a query to only include featured documents
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include documents with public access
     */
    public function scopePublic($query)
    {
        return $query->where('access_level', self::ACCESS_PUBLIC);
    }

    /**
     * Scope a query to only include documents by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to only include documents by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if document is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if document is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if document is accessible by user
     */
    public function isAccessibleBy(User $user = null): bool
    {
        if ($this->access_level === self::ACCESS_PUBLIC) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($this->access_level === self::ACCESS_INTERNAL_ONLY) {
            return $user->hasRole(['super_admin', 'project_owner', 'communication_content_manager', 'technical_advisor']);
        }

        if ($this->access_level === self::ACCESS_PARTNER_ONLY) {
            return $user->hasRole(['super_admin', 'project_owner', 'communication_content_manager', 'technical_advisor', 'partner_university_contributor']);
        }

        return false;
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size)
            return '0 B';

        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('admin.document.download', $this->id);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function getTagsListAttribute(): string
    {
        return $this->tags ? $this->tags->pluck('name')->implode(', ') : '';
    }
}