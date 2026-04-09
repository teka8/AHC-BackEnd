<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class NewsletterArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'others_id',
        'title',
        'volume',
        'issue_number',
        'image_path',
        'content',
        'sort_order',
    ];

    /**
     * Get the newsletter (Others) that this article belongs to.
     */
    public function others(): BelongsTo
    {
        return $this->belongsTo(Others::class, 'others_id');
    }

    /**
     * Get the full URL for the article image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return url('storage/' . $this->image_path);
    }
}
