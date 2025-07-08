<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id'
    ];

    /**
     * Get the users that belong to the team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the projects that belong to the team.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }


    /**
     * Add a user to the team with a specific role
     */
    public function addUsers($users, $role = 'member')
{
    foreach ($users as $user) {
        $userObj = $user instanceof User ? $user : User::findOrFail($user);
        $this->users()->syncWithoutDetaching($userObj->id);
        $userObj->assignRole($role, $this->id);
    }
}

    /**
     * Remove a user from the team
     */
    public function removeUsers($users)
    {
        foreach ($users as $user) {
            $userObj = $user instanceof User ? $user : User::findOrFail($user);
            // Remove all team roles from user

            $userRoles = $userObj->getTeamRoles($this->id);
            foreach ($userRoles as $role) {
                $userObj->removeRole($role, $this->id);
            }
            $this->users()->detach($userObj->id);
        }
    }

    /**
     * Check if user is a member of this team
     */
    public function teamMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's role in this team
     */
    public function getUserRole(User $user)
    {
        $pivot = $this->users()->where('user_id', $user->id)->first();
        return $pivot ? $pivot->pivot->role : null;
    }

    /**
     * Create a role for this team
     */
    public function createRole($name, $permissions = [])
    {
        $role = Role::firstOrCreate([
            'name' => $name,
            'guard_name' => 'sanctum',
            'team_id' => $this->id,
        ]);

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    /**
     * Create a permission for this team
     */
    public function createPermission($name)
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => 'sanctum',
            'team_id' => $this->id,
        ]);
    }
}
