<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUserStamps;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;

class Page extends Model implements HasMedia
{
    use SoftDeletes, HasUserStamps, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    // Relationships
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('display_order');
    }

    public function activeSections(): HasMany
    {
        return $this->hasMany(PageSection::class)->where('is_active', true)->orderBy('display_order');
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
        $this->addMediaCollection('hero_image')
            ->singleFile()
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

    // Accessors & Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Helper Methods
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getUrlAttribute()
    {
        return route('pages.show', $this->slug);
    }
}
