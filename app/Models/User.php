<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasComments;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,HasRoles,HasComments;
    
    /**
     * The guard name for Spatie permissions
     */
    protected $guard_name = 'sanctum';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the teams that the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    /**
     * Get the projects that the user belongs to.
     */
   
     public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
          ;
    }

    /**
     * Get the tasks assigned to the user.
     */

    public function tasksAssigned() {
        return $this->hasMany(Task::class, 'assigned_to_user_id');
    }

    /**
     * Get the tasks created by the user.
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

   

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }


    
    /**
     * Check if user has permission in a specific team
     */
    public function hasTeamPermission($permission, $team = null): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($team) {
            return $this->hasPermissionTo($permission, $team->id);
        }

        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if user has role in a specific team
     */
    public function hasTeamRole($role, $team = null): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        if ($team) {
            return $this->hasRole($role, $team->id);
        }

        return $this->hasRole($role);
    }

    /**
     * Get user's roles for a specific team
     */
    public function getTeamRoles($team = null)
    {
        if ($team) {
            return $this->roles()->where('team_id', $team->id)->get();
        }

        return $this->roles;
    }

   
    
  
}
