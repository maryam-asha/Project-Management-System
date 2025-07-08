<?php

namespace App\Http\Controllers\Api;
 
 use App\Models\Project;
 use App\Models\Team;
 use App\Services\ProjectService;
 use Illuminate\Http\Request;
 use App\Services\AttachmentService;
 use App\Http\Controllers\Controller;
 use App\Http\Requests\Project\StoreProjectRequest;
 use App\Http\Requests\Project\UpdateProjectRequest;
 use App\Http\Resources\ProjectResource;
 
 class ProjectController extends Controller
 {
 
     protected ProjectService $service;
     protected AttachmentService $AttachmentService;

     /**
      * ProjectController constructor.
      *
      * @param ProjectService $service
      * @param AttachmentService $AttachmentService
      */
     public function __construct(ProjectService $service)
     {
         $this->middleware('auth:sanctum');
         $this->service = $service;
     }
 
     /**
      * Display a listing of the projects.
      *
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
      */
     public function index()
     {
         $this->authorize('viewAny', Project::class);
         $projects = $this->service->list();
         return ProjectResource::collection($projects);
     }
 
     /**
      * Store a newly created project in storage.
      *
      * @param StoreProjectRequest $request
      * @return ProjectResource
      */
     public function store(StoreProjectRequest $request)
     {
        $team = Team::findOrFail($request->input('team_id'));
        $this->authorize('create', [Project::class, $team]);
        try {
            $data = $request->validated();
            $data['created_by_user_id'] = $request->user()->id;
            $project = $this->service->create($data);
            $project->load(['team', 'users', 'tasks']);
            return $this->successResponse(new ProjectResource($project), 'Project created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
     }
 
     /**
      * Display the specified project.
      *
      * @param Project $project
      * @return ProjectResource
      */
     public function show(Project $project)
     {
         $this->authorize('view', $project);
         $project->load(['team', 'users', 'tasks']);
         return new ProjectResource($project);
     }
 
     /**
      * Update the specified project in storage.
      *
      * @param UpdateProjectRequest $request
      * @param Project $project
      * @return ProjectResource
      */
     public function update(UpdateProjectRequest $request, Project $project)
     {
        $this->authorize('update', $project);
        try {
            $updated = $this->service->update($project, $request->validated());
            $updated->load(['team', 'users', 'tasks']);
            return $this->successResponse(new ProjectResource($updated), 'Project updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
     }
 
     /**
      * Remove the specified project from storage.
      *
      * @param Project $project
      * @return \Illuminate\Http\JsonResponse
      */
     public function destroy(Project $project)
     {
        $this->authorize('delete', $project);
        try {
            $this->service->delete($project);
            return $this->successResponse(null, 'Project deleted', 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
     }
 
    /**
     * Add one or more users to a project.
     *
     * @param \Illuminate\Http\Request $request
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUsers(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            // 'role' => 'sometimes|string',
        ]);
        // $role = $request->input('role', 'member');
        $project->addUser($request->input('user_ids'));
        $project->load('users');
        return response()->json([
            'message' => 'Users added to project',
            'users' => $project->users,
        ]);
    }

    /**
     * Remove one or more users from a project.
     *
     * @param \Illuminate\Http\Request $request
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeUsers(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        $project->users()->detach($request->input('user_ids'));
        $project->load('users');
        return response()->json([
            'message' => 'Users removed from project',
            'users' => $project->users,
        ]);
    }
 
    public function assignTeam(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);
        $project->team_id = $request->input('team_id');
        $project->save();
        $project->load('team');
        return $this->successResponse(new ProjectResource($project), 'Team assigned to project');
    }
 }
 