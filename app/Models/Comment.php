<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Comment extends Model
{
    use HasAttachments,HasFactory;
    protected $fillable = [
        'content',
        'user_id',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * Mutator: Sanitize content
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = strip_tags($value);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
    
}
