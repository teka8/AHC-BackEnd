<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'publication_date',
        'abstract',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'mime_type',
        'document_type',
        'category',
        'tags',
        'version',
        'previous_version_id',
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
        'publication_date' => 'date',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    protected $attributes = [
        'version' => '1.0',
        'is_featured' => false,
        'access_level' => 'public',
        'status' => 'draft',
        'download_count' => 0,
        'view_count' => 0,
    ];

    // Document type constants
    const TYPE_RESEARCH_PAPER = 'Research Paper';
    const TYPE_POLICY_BRIEF = 'Policy Brief';
    const TYPE_REPORT = 'Report';
    const TYPE_GUIDELINE = 'Guideline';
    const TYPE_EDUCATIONAL_CONTENT = 'Educational Content';
    const TYPE_OTHER = 'Other';
    
    // Available document types for forms/validation
    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_RESEARCH_PAPER,
            self::TYPE_POLICY_BRIEF,
            self::TYPE_REPORT,
            self::TYPE_GUIDELINE,
            self::TYPE_EDUCATIONAL_CONTENT,
            self::TYPE_OTHER,
        ];
    }

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
     * Document workflow states
     */
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the user who created the document
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the document
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the document
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
        return $this->belongsTo(Document::class, 'previous_version_id');
    }

    /**
     * Get all tags associated with the document
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(DocumentTag::class, 'document_tag');
    }

    /**
     * Get all access logs for this document
     */
    public function accessLogs()
    {
        return $this->hasMany(DocumentAccessLog::class);
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
            return $user->hasRole(['Superadmin', 'project_owner', 'communication_content_manager', 'technical_advisor']);
        }

        if ($this->access_level === self::ACCESS_PARTNER_ONLY) {
            return $user->hasRole(['Superadmin', 'project_owner', 'communication_content_manager', 'technical_advisor', 'partner_university_contributor']);
        }

        return false;
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
        if (!$this->tags) {
            return '';
        }
        
        // If tags is already an array (JSON field)
        if (is_array($this->tags)) {
            return implode(', ', $this->tags);
        }
        
        // If tags is a relationship collection
        if (is_object($this->tags) && method_exists($this->tags, 'pluck')) {
            return $this->tags->pluck('name')->implode(', ');
        }
        
        return '';
    }


    /**
     * Get preview URL
     */
    public function getPreviewUrlAttribute(): string
    {
        return route('admin.document.preview', $this->id);
    }

    /**
     * Check if document can be previewed (PDF files)
     */
    public function getCanPreviewAttribute(): bool
    {
        return $this->file_extension === 'pdf';
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
            if ($user->hasPermissionTo($config['required_permission']) || $user->hasRole('super_admin')) {
                $availableActions[$action] = $config;
            }
        }

        return $availableActions;
    }

    /**
     * Check if user can perform specific action
     */
    public function canPerformAction($action, User $user = null)
    {
        $user = $user ?: auth()->user();
        $availableActions = $this->getAvailableActions($user);
        
        return isset($availableActions[$action]);
    }

    

    /**
     * Check if action is allowed
     */
    public function canTransitionTo($targetStatus, User $user = null)
    {
        $user = $user ?: auth()->user();
        $availableActions = $this->getAvailableActions($user);
        
        foreach ($availableActions as $action => $config) {
            if ($config['target'] === $targetStatus) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get status badge color
     */
    public function getStatusColor()
    {
        $colors = [
            self::STATUS_DRAFT => 'gray',
            self::STATUS_UNDER_REVIEW => 'yellow', 
            self::STATUS_APPROVED => 'green',
            self::STATUS_PUBLISHED => 'blue',
            self::STATUS_ARCHIVED => 'orange',
            self::STATUS_REJECTED => 'red'
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get status display text
     */
    public function getStatusDisplay()
    {
        $display = [
            self::STATUS_DRAFT => __('Draft'),
            self::STATUS_UNDER_REVIEW => __('Under Review'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PUBLISHED => __('Published'),
            self::STATUS_ARCHIVED => __('Archived'),
            self::STATUS_REJECTED => __('Changes Requested')
        ];
        
        return $display[$this->status] ?? $this->status;
    }
}