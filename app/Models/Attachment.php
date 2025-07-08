<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'path',
        'disk',
        'file_name',
        'mime_type',
        'size',
        'attachable_id',
        'attachable_type',
    ];

    protected $casts = [
        'size' => 'integer'
    ];
    
    public function attachable():MorphTo
    {
        return $this->morphTo();
    }
}
