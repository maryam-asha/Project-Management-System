<?php

namespace App\Traits;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttachments
{
    /**
     * Get all of the model's attachments.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Add an attachment to the model.
     */
    public function addAttachment(array $attributes)
    {
        return $this->attachments()->create($attributes);
    }

    /**
     * Remove an attachment from the model.
     */
    public function removeAttachment($attachmentId)
    {
        return $this->attachments()->where('id', $attachmentId)->delete();
    }
} 