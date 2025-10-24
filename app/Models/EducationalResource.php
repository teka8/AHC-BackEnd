<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EducationalResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'creator',
        'description',
        'learning_objectives',
        'resource_type',
        'educational_level',
        'subject_area',
        'duration_minutes',
        'language',
        'file_path',
        'file_name',
        'file_size',
        'embed_code',
        'thumbnail_path',
        'tags',
        'difficulty_level',
        'is_featured',
        'access_level',
        'requires_enrollment',
        'status',
        'published_at',
        'created_by',
        'updated_by',
        'approved_by',
        'view_count',
        'completion_count',
        'download_count',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'requires_enrollment' => 'boolean',
        'published_at' => 'datetime',
        'duration_minutes' => 'integer',
        'view_count' => 'integer',
        'completion_count' => 'integer',
        'download_count' => 'integer',
    ];

    protected $attributes = [
        'educational_level' => 'All Levels',
        'language' => 'English',
        'is_featured' => false,
        'access_level' => 'public',
        'requires_enrollment' => false,
        'status' => 'draft',
        'view_count' => 0,
        'completion_count' => 0,
        'download_count' => 0,
    ];

    // Resource type constants
    const TYPE_VIDEO = 'Video';
    const TYPE_PODCAST = 'Podcast';
    const TYPE_INTERACTIVE_MODULE = 'Interactive Module';
    const TYPE_LESSON_PLAN = 'Lesson Plan';
    const TYPE_TEACHING_GUIDE = 'Teaching Guide';
    const TYPE_PRESENTATION = 'Presentation';
    const TYPE_CASE_STUDY = 'Case Study';
    const TYPE_SIMULATION = 'Simulation';
    const TYPE_OTHER = 'Other';

    // Educational level constants
    const LEVEL_UNDERGRADUATE = 'Undergraduate';
    const LEVEL_POSTGRADUATE = 'Postgraduate';
    const LEVEL_FACULTY_DEVELOPMENT = 'Faculty Development';
    const LEVEL_CONTINUING_EDUCATION = 'Continuing Education';
    const LEVEL_ALL_LEVELS = 'All Levels';

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
     * Get the user who created the resource
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the resource
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the resource
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all tags associated with the resource
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(EducationalResourceTag::class, 'educational_resource_tag');
    }

    /**
     * Get all access logs for this resource
     */
    public function accessLogs()
    {
        return $this->hasMany(EducationalResourceAccessLog::class);
    }

    /**
     * Scope a query to only include published resources
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope a query to only include featured resources
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include resources with public access
     */
    public function scopePublic($query)
    {
        return $query->where('access_level', self::ACCESS_PUBLIC);
    }

    /**
     * Scope a query to only include resources by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('resource_type', $type);
    }

    /**
     * Scope a query to only include resources by educational level
     */
    public function scopeByEducationalLevel($query, $level)
    {
        return $query->where('educational_level', $level);
    }

    /**
     * Check if resource is published
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if resource is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if resource is accessible by user
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
     * Get duration in human readable format
     */
    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_minutes) return 'N/A';

        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' min';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($minutes === 0) {
            return $hours . ' hr';
        }

        return $hours . ' hr ' . $minutes . ' min';
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '0 B';

        $size = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Check if resource has embedded content
     */
    public function hasEmbeddedContent(): bool
    {
        return !empty($this->embed_code);
    }

    /**
     * Check if resource has downloadable file
     */
    public function hasDownloadableFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('admin.education.download', $this->id);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment completion count
     */
    public function incrementCompletionCount(): void
    {
        $this->increment('completion_count');
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Available transitions for each status with permission requirements
     */
    public static function getAvailableTransitions($currentStatus, User $user = null)
    {
        $transitions = [
            self::STATUS_DRAFT => [
                'send_for_review' => [
                    'target' => self::STATUS_UNDER_REVIEW,
                    'label' => __('Send for Review'),
                    'color' => 'yellow',
                    'icon' => 'lucide:send',
                    'required_permission' => 'document.review'
                ],
                'publish' => [
                    'target' => self::STATUS_PUBLISHED,
                    'label' => __('Publish Directly'),
                    'color' => 'green',
                    'icon' => 'lucide:globe',
                    'required_permission' => 'document.publish'
                ]
            ],
            self::STATUS_UNDER_REVIEW => [
                'approve' => [
                    'target' => self::STATUS_APPROVED,
                    'label' => __('Approve'),
                    'color' => 'green',
                    'icon' => 'lucide:check-circle',
                    'required_permission' => 'document.approve'
                ],
                'reject' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Request Changes'),
                    'color' => 'red',
                    'icon' => 'lucide:arrow-left',
                    'required_permission' => 'document.approve'
                ],
                'publish' => [
                    'target' => self::STATUS_PUBLISHED,
                    'label' => __('Publish'),
                    'color' => 'green',
                    'icon' => 'lucide:globe',
                    'required_permission' => 'document.publish'
                ]
            ],
            self::STATUS_APPROVED => [
                'publish' => [
                    'target' => self::STATUS_PUBLISHED,
                    'label' => __('Publish'),
                    'color' => 'green',
                    'icon' => 'lucide:globe',
                    'required_permission' => 'document.publish'
                ],
                'send_back' => [
                    'target' => self::STATUS_UNDER_REVIEW,
                    'label' => __('Send Back for Review'),
                    'color' => 'yellow',
                    'icon' => 'lucide:arrow-left',
                    'required_permission' => 'document.review'
                ]
            ],
            self::STATUS_PUBLISHED => [
                'unpublish' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Unpublish'),
                    'color' => 'gray',
                    'icon' => 'lucide:eye-off',
                    'required_permission' => 'document.unpublish'
                ],
                'archive' => [
                    'target' => self::STATUS_ARCHIVED,
                    'label' => __('Archive'),
                    'color' => 'orange',
                    'icon' => 'lucide:archive',
                    'required_permission' => 'document.archive'
                ]
            ],
            self::STATUS_ARCHIVED => [
                'restore' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Restore'),
                    'color' => 'blue',
                    'icon' => 'lucide:refresh-cw',
                    'required_permission' => 'document.archive'
                ]
            ]
        ];

        return $transitions[$currentStatus] ?? [];
    }

    /**
     * Get available actions for current user based on permissions
     */
    public function getAvailableActions(User $user = null)
    {
        $user = $user ?: auth()->user();
        $transitions = self::getAvailableTransitions($this->status, $user);
        $availableActions = [];

        foreach ($transitions as $action => $config) {
            if ($user && (method_exists($user, 'hasPermissionTo') ? $user->hasPermissionTo($config['required_permission']) : false || (method_exists($user, 'hasRole') && $user->hasRole('super_admin')))) {
                $availableActions[$action] = $config;
            }
        }

        return $availableActions;
    }

    public function canPerformAction($action, User $user = null)
    {
        $user = $user ?: auth()->user();
        $availableActions = $this->getAvailableActions($user);
        return isset($availableActions[$action]);
    }

    public function getStatusColor()
    {
        $colors = [
            self::STATUS_DRAFT => 'gray',
            self::STATUS_UNDER_REVIEW => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_PUBLISHED => 'blue',
            self::STATUS_ARCHIVED => 'orange',
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function getStatusDisplay()
    {
        $display = [
            self::STATUS_DRAFT => __('Draft'),
            self::STATUS_UNDER_REVIEW => __('Under Review'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PUBLISHED => __('Published'),
            self::STATUS_ARCHIVED => __('Archived'),
        ];
        return $display[$this->status] ?? $this->status;
    }
}