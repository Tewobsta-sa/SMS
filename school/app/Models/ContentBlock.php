<?php

namespace App\Models;

use App\Enums\ContentTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUserStamps;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ContentBlock extends Model implements HasMedia
{
    use SoftDeletes, HasUserStamps, InteractsWithMedia;

    protected $fillable = [
        'section_id',
        'type',
        'title',
        'slug', 
        'icon',
        'subtitle',
        'short_description',
        'content',
        'list_items',
        'video_url',
        'metadata',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'type' => ContentTypeEnum::class,
        'list_items' => 'array',
        'metadata' => 'array',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'section_id');
    }

    // Accessor for getting page through section
    public function getPageAttribute()
    {
        return $this->section?->page;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(300)
                    ->height(200)
                    ->sharpen(10);
            });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isList(): bool
    {
        return $this->type === 'list';
    }

    public function isTimeline(): bool
    {
        return $this->type === 'timeline';
    }

    public function isGallery(): bool
    {
        return $this->type === 'gallery';
    }
    
}
