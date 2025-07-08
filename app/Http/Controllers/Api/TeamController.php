<?php
 
 namespace App\Http\Controllers\Api;
 
 use App\Models\Team;
 use App\Services\TeamService;
 use App\Http\Resources\TeamResource;
 use App\Http\Requests\Team\StoreTeamRequest;
 use App\Http\Requests\Team\UpdateTeamRequest;
 use Illuminate\Http\Request;
 use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    protected TeamService $service;

    public function __construct(TeamService $service)
    {
        $this->middleware('auth:sanctum');
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('viewAny', Team::class);
        $teams = $this->service->list();
        return TeamResource::collection($teams);
    }

    public function store(StoreTeamRequest $request)
    {
        $this->authorize('create', Team::class);
        try {
         
            $data = $request->validated();
            $data['owner_id'] = $request->user()->id;
            $team = $this->service->create($data);
            $team->load(['users', 'owner', 'projects']);
            return $this->successResponse(new TeamResource($team), 'Team created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $team->load(['users', 'owner', 'projects']);
        return new TeamResource($team);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);
        try {
            $updated = $this->service->update($team, $request->validated());
            $updated->load(['users', 'owner', 'projects']);
            return $this->successResponse(new TeamResource($updated), 'Team updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        try {
            $this->service->delete($team);
            return $this->successResponse(null, 'Team deleted', 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function addUsers(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'sometimes|string',
        ]);
        $role = $request->input('role', 'member');
        $team->addUsers($request->input('user_ids'), $role);
        $team->load('users');
        return $this->successResponse(new TeamResource($team), 'Users added to team');
    }
    public function removeUsers(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        $team->removeUsers($request->input('user_ids'));
        $team->load('users');
        return $this->successResponse(new TeamResource($team), 'Users removed from team');
    }
   
}