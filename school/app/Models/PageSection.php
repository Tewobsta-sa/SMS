<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUserStamps;

class PageSection extends Model
{
    use SoftDeletes, HasUserStamps;

    protected $fillable = [
        'page_id',
        'title',
        'subtitle',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    // Relationships
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function contentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class, 'section_id')->orderBy('display_order');
    }

    public function activeContentBlocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
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
}
