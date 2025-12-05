<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'hero_description',
        'hero_image',
        'section',
        'is_custom_section',
        'meta_title',
        'meta_description',
        'status',
        'show_in_nav',
        'show_in_footer',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_custom_section' => 'boolean',
        'show_in_nav' => 'boolean',
        'show_in_footer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Status constants for easy reference.
     */
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Predefined section constants.
     */
    public const SECTION_ABOUT = 'about';
    public const SECTION_TERMS = 'terms';
    public const SECTION_PRIVACY = 'privacy';
    public const SECTION_CONTACT = 'contact';
    public const SECTION_FAQ = 'faq';
    public const SECTION_SHIPPING = 'shipping';
    public const SECTION_RETURNS = 'returns';

    /**
     * Get the user who created the page.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the page.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope a query to only include draft pages.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope a query to only include archived pages.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Scope a query to only include pages visible in navigation.
     */
    public function scopeVisibleInNav($query)
    {
        return $query->where('show_in_nav', true);
    }

    /**
     * Scope a query to only include pages visible in footer.
     */
    public function scopeVisibleInFooter($query)
    {
        return $query->where('show_in_footer', true);
    }

    /**
     * Scope a query to only include pages by section.
     */
    public function scopeBySection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope a query to only include custom sections.
     */
    public function scopeCustomSections($query)
    {
        return $query->where('is_custom_section', true);
    }

    /**
     * Scope a query to only include predefined sections.
     */
    public function scopePredefinedSections($query)
    {
        return $query->where('is_custom_section', false);
    }

    /**
     * Check if the page is published.
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Check if the page is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if the page is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Check if the page uses a custom section.
     */
    public function isCustomSection(): bool
    {
        return $this->is_custom_section;
    }

    /**
     * Check if the page is visible in navigation.
     */
    public function isVisibleInNav(): bool
    {
        return $this->show_in_nav && $this->isPublished();
    }

    /**
     * Check if the page is visible in footer.
     */
    public function isVisibleInFooter(): bool
    {
        return $this->show_in_footer && $this->isPublished();
    }

    /**
     * Get the page URL for frontend.
     */
    public function getUrlAttribute(): string
    {
        return route('admin.pages.show', $this->id);
    }

    /**
     * Get the edit URL for backend.
     */
    public function getEditUrlAttribute(): string
    {
        return route('admin.pages.edit', $this->id);
    }

    /**
     * Get the preview URL for frontend.
     */
    public function getPreviewUrlAttribute(): string
    {
        if ($this->isPublished()) {
            return $this->url;
        }

        // For draft pages, you might want to add a preview token or similar
        return route('pages.preview', ['slug' => $this->slug]);
    }

    /**
     * Get available status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PUBLISHED => __('Published'),
            self::STATUS_DRAFT => __('Draft'),
            self::STATUS_ARCHIVED => __('Archived'),
        ];
    }

    /**
     * Get predefined section options.
     */
    public static function getPredefinedSections(): array
    {
        return [
            self::SECTION_ABOUT => __('About Us'),
            self::SECTION_TERMS => __('Terms & Conditions'),
            self::SECTION_PRIVACY => __('Privacy Policy'),
            self::SECTION_CONTACT => __('Contact Information'),
            self::SECTION_FAQ => __('FAQ'),
            self::SECTION_SHIPPING => __('Shipping Policy'),
            self::SECTION_RETURNS => __('Return Policy'),
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate slug automatically if not provided
        static::creating(function ($page) {
            if (empty($page->slug) && !empty($page->title)) {
                $page->slug = \Illuminate\Support\Str::slug($page->title);
            }
        });

        // Update updated_by when saving
        static::updating(function ($page) {
            if (auth()->check()) {
                $page->updated_by = auth()->id();
            }
        });

        // Set created_by when creating
        static::creating(function ($page) {
            if (auth()->check()) {
                $page->created_by = auth()->id();
                $page->updated_by = auth()->id();
            }
        });
    }
}