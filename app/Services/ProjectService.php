<?php

namespace App\Services;

use App\Models\Project;
use App\Jobs\SendAdConfirmationEmail;
use Illuminate\Support\Facades\Cache;

class ProjectService
{
    
    /**
     * Get a paginated list of projects with relationships.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list()
    {
        return Cache::remember('projects', 60, function () {
            return Project::with(['users', 'tasks', 'team', 'creator', 'comments', 'attachments'])
                ->orderByDesc('created_at')
                ->paginate(15);
        });
    }

    /**
     * Get a list of popular projects (by most tasks).
     */
    public function getPopularProjects()
    {
        return \Cache::remember('popular_projects', 60, function () {
            return \App\Models\Project::withCount('tasks')
                ->orderByDesc('tasks_count')
                ->take(10)
                ->get();
        });
    }

    /**
     * Get a list of most active teams (by most projects).
     */
    public function getMostActiveTeams()
    {
        return \Cache::remember('most_active_teams', 60, function () {
            return \App\Models\Team::withCount('projects')
                ->orderByDesc('projects_count')
                ->take(10)
                ->get();
        });
    }

    /**
     * Get the completed task count for a project (cached).
     */
    public function getCompletedTaskCount($projectId)
    {
        $cacheKey = "project:{$projectId}:completed_task_count";
        return \Cache::remember($cacheKey, 60, function () use ($projectId) {
            return \App\Models\Task::where('project_id', $projectId)
                ->where('status', 'completed')
                ->count();
        });
    }

    /**
     * Create a new project.
     *
     * @param array $data
     * @param int $userId
     * @return Project
     */
    public function create(array $data, int $userId)
    {
        $project = Project::create(array_merge($data, [
            'created_by_user_id' => $userId,
            'status' => 'pending',
        ]));
        Cache::forget('projects');
        Cache::forget('popular_projects');
        Cache::forget('most_active_teams');
        return $project;
    }

    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data)
    {   
        $project->update($data);
        Cache::forget('projects');
        Cache::forget('popular_projects');
        Cache::forget('most_active_teams');
        return $project;
    }

    /**
     * Delete a project.
     *
     * @param Project $project
     * @return bool
     */
    public function delete(Project $project): bool
    {
        $result = $project->delete();
        Cache::forget('projects');
        Cache::forget('popular_projects');
        Cache::forget('most_active_teams');
        Cache::forget("project:{$project->id}:completed_task_count");
        return $result;
    }

   
}