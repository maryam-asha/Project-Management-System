<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\HasAttachments;
use App\Traits\HasComments;
class Project extends Model
{
    use HasFactory ,HasAttachments,HasComments;

    protected $fillable = [
        'name',
        'description',  
        'status',
        'due_date',
        'team_id',
        'created_by_user_id'
    ];

    /**
     * Scope: Active projects
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
        $this->attributes['description'] = strip_tags($value);
    }

    /**
     * Get the users that belong to the project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    /**
     * Get the tasks for this project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the team that owns this project.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user who created this project.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

   
   

    /**
     * Check if user has permission to access this project
     */
    public function userHasPermission(User $user, $permission): bool
    {
        // Check if user is admin
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Check if user is a member of the project's team
        if ($this->team && $this->team->hasUser($user)) {
            return $user->hasTeamPermission($permission, $this->team);
        }

        // Check if user is a direct member of the project
        if ($this->users()->where('user_id', $user->id)->exists()) {
            return $user->hasPermissionTo($permission);
        }

        return false;
    }

    /**
     * Add one or more users to the project with a specific role.
     *
     * @param User|array $users
     * @param string $role
     */
    public function addUser($users)
    {
        $this->users()->syncWithoutDetaching($users);
    }

    /**
     * Remove a user from the project
     */
    public function removeUser(User $user)
    {
        $this->users()->detach($user->id);
    }

    /**
     * Get user's role in this project
     */
    public function getUserRole(User $user)
    {
        $pivot = $this->users()->where('user_id', $user->id)->first();
        return $pivot ? $pivot->pivot->role : null;
    }
}
