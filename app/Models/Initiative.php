<?php

namespace App\Models;

use Database\Factories\InitiativeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string|null $image
 * @property string|null $image_url
 * @property int $views_count
 */
class Initiative extends Model
{
    /** @use HasFactory<InitiativeFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'image'];

    protected $appends = ['image_url', 'views_count'];

    public function getViewsCountAttribute(): int
    {
        return $this->getAttributeFromArray('views_count') ?? 0;
    }

    protected static function booted(): void
    {
        static::deleting(function (Initiative $initiative) {
            if ($initiative->image) {
                Storage::disk('public')->delete($initiative->image);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
