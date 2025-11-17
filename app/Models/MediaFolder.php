<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MediaFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'parent_id',
        'created_by',
        'updated_by',
        'order_column',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'order_column' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (MediaFolder $folder): void {
            if (empty($folder->uuid)) {
                $folder->uuid = (string) Str::uuid();
            }

            if (empty($folder->slug) && ! empty($folder->name)) {
                $folder->slug = static::generateUniqueSlug($folder->name);
            }

            if (! isset($folder->order_column)) {
                $maxOrder = static::where('parent_id', $folder->parent_id)->max('order_column');
                $folder->order_column = is_null($maxOrder) ? 0 : $maxOrder + 1;
            }
        });

        static::saving(function (MediaFolder $folder): void {
            if (empty($folder->slug) && ! empty($folder->name)) {
                $folder->slug = static::generateUniqueSlug($folder->name, $folder->id);
            }
        });
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order_column');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'folder_id')
            ->where('collection_name', 'folder_media')
            ->orderByDesc('created_at');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
