<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class News extends Post
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'user_id',
        'parent_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        parent::booted();
        
        static::addGlobalScope('news', function (Builder $builder) {
            $builder->where('post_type', 'news');
        });

        static::creating(function ($model) {
            $model->post_type = 'news';
        });
    }

    /**
     * Get the post meta.
     */
    public function postMeta(): HasMany
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    /**
     * The terms that belong to the post.
     */
    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'term_relationships', 'post_id');
    }
}