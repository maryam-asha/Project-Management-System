<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    protected $cacheKey = 'tasks';

    /**
     * Get a paginated list of tasks with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [])
    {
        $cacheKey = $this->cacheKey . ':' . md5(json_encode($filters));
        return Cache::remember($cacheKey, 60, function () use ($filters) {
            $query = Task::with(['assignedUser', 'project', 'comments', 'attachments'])
                ->orderByDesc('created_at');
            if (!empty($filters['project_id'])) {
                $query->where('project_id', $filters['project_id']);
            }
            if (!empty($filters['assigned_to_user_id'])) {
                $query->where('assigned_to_user_id', $filters['assigned_to_user_id']);
            }
            return $query->paginate(15);
        });
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @param int $userId
     * @return Task
     */
    public function create(array $data, int $userId)
    {
        $task = Task::create(array_merge($data, [
            'created_by_user_id' => $userId,
            'status' => 'pending',
        ]));
        \Cache::forget('popular_projects');
        \Cache::forget("project:{$task->project_id}:completed_task_count");
        return $task;
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data)
    {
        $oldStatus = $task->status;
        $task->update($data);
        \Cache::forget('popular_projects');
        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            \Cache::forget("project:{$task->project_id}:completed_task_count");
        }
        return $task;
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        $result = $task->delete();
        \Cache::forget('popular_projects');
        \Cache::forget("project:{$task->project_id}:completed_task_count");
        return $result;
    }
}