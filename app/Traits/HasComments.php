<?php

namespace App\Traits;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;


trait HasComments
{
    /**
     * Get all of the model's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(\App\Models\Comment::class, 'commentable');
    }

    /**
     * Add a comment to the model.
     */
    public function addComment(array $attributes)
    {
        return $this->comments()->create($attributes);
    }

    /**
     * Remove a comment from the model.
     */
    public function removeComment($commentId)
    {
        return $this->comments()->where('id', $commentId)->delete();
    }
}