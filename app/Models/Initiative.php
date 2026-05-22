<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property string|null $image
 * @property string|null $image_url
 */
class Initiative extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'image'];

    protected $appends = ['image_url'];

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

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
