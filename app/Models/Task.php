<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\HasAttachments;
use App\Traits\HasComments;
class Task extends Model
{
    use HasFactory, HasAttachments,HasComments;

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'due_date',
        'project_id',
        'assigned_to_user_id',
        'created_by_user_id',
    ];

    /**
     * Get the user assigned to this task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }


    /**
     * Get the project that this task belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    
    /**
     * Get the attachments for this task.
     */
  
    /**
     * Check if user has permission to access this task
     */
    public function userHasPermission(User $user, $permission): bool
    {
        // Check if user is admin
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Check if user is assigned to this task
        if ($this->assigned_to_user_id === $user->id) {
            return $user->hasPermissionTo($permission);
        }

        // Check if user is the creator of this task
        if ($this->created_by_user_id === $user->id) {
            return $user->hasPermissionTo($permission);
        }

        // Check if user has permission through the project's team
        if ($this->project) {
            return $this->project->userHasPermission($user, $permission);
        }

        return false;
    }

    /**
     * Check if user can edit this task
     */
    public function userCanEdit(User $user): bool
    {
        return $this->userHasPermission($user, 'edit-tasks') ||
               $this->assigned_to_user_id === $user->id ||
               $this->created_by_user_id === $user->id;
    }

    /**
     * Check if user can delete this task
     */
    public function userCanDelete(User $user): bool
    {
        return $this->userHasPermission($user, 'delete-tasks') ||
               $this->created_by_user_id === $user->id;
    }

    /**
     * Check if user can assign this task
     */
    public function userCanAssign(User $user): bool
    {
        return $this->userHasPermission($user, 'assign-tasks');
    }

    /**
     * Accessor: Get formatted due date
     */
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? \Carbon\Carbon::parse($this->due_date)->format('d/m/Y') : null;
    }

    

    /**
     * Mutator: Sanitize description
     */
    public function setDescriptionAttribute($value)
    {
        // Remove script tags and their content
        $value = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
        $this->attributes['description'] = strip_tags($value);
    }

    /**
     * Scope: Overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope: Completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
