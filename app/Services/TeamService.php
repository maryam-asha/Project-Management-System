<?php

namespace App\Services;


use Illuminate\Support\Facades\Cache;
use App\Models\Team;

class TeamService
{
    protected $cacheKey = 'teams';

    /**
     * Get a paginated list of teams with relationships.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list()
    {
        return Cache::remember($this->cacheKey, 60, function () {
            return Team::with(['users', 'owner', 'projects'])
                ->orderByDesc('created_at')
                ->paginate(15);
        });
    }

    /**
     * Create a new team.
     *
     * @param array $data
     * @return Team
     */
    public function create(array $data)
    {
        $team = Team::create($data);
        Cache::forget($this->cacheKey);
        return $team;
    }

    /**
     * Update an existing team.
     *
     * @param Team $team
     * @param array $data
     * @return Team
     */
    public function update(Team $team, array $data)
    {
        $team->update($data);
        Cache::forget($this->cacheKey);
        return $team;
    }

    /**
     * Delete a team.
     *
     * @param Team $team
     * @return bool
     */
    public function delete(Team $team): bool
    {
        $result = $team->delete();
        Cache::forget($this->cacheKey);
        return $result;
    }
}