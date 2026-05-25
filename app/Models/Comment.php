<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $initiative_id
 * @property int $user_id
 * @property string $body
 */
#[Fillable(['initiative_id', 'user_id', 'body'])]
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(Initiative::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
