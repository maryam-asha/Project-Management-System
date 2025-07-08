<?php
 
 namespace App\Http\Controllers\Api;
 
 use App\Models\Task;
 use App\Models\Priject;
 use App\Services\TaskService;
 use App\Http\Resources\TaskResource;
 use App\Http\Requests\Task\StoreTaskRequest;
 use App\Http\Requests\Task\UpdateTaskRequest;
 use Illuminate\Http\Request;
 use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->middleware('auth:sanctum');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
       
        $filters = [
            'project_id' => $request->query('project_id'),
            'assigned_to_user_id' => $request->query('assigned_to_user_id'),
        ];
        $tasks = $this->service->list(array_filter($filters));
        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $project = Project::findOrFail($request->input('project_id'));
        $this->authorize('create', [Task::class, $project]);
        try {
        
            $task = $this->service->create(
                $request->validated(),
                $request->user()->id
            );
            $task->load(['assignedUser', 'project', 'comments', 'attachments']);
            return $this->successResponse(new TaskResource($task), 'Task created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
      
        $task->load(['assignedUser', 'project', 'comments', 'attachments']);
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        try {
          
            $updated = $this->service->update($task, $request->validated());
            $updated->load(['assignedUser', 'project', 'comments', 'attachments']);
            return $this->successResponse(new TaskResource($updated), 'Task updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        try {
            $this->service->delete($task);
            return $this->successResponse(null, 'Task deleted', 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}