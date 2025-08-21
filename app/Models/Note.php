<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

class Note extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasTags;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'status',
        'priority',
        'is_pinned',
        'is_favorite',
        'metadata',
        'user_id',
        'category_id',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_favorite' => 'boolean',
        'metadata' => 'array',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function getWordCountAttribute()
    {
        return str_word_count(strip_tags($this->content));
    }

    public function getReadingTimeAttribute()
    {
        return ceil($this->word_count / 200) . ' dk';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf', 'text/plain']);

        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(500)
            ->height(500);
    }
}
