<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use App\Models\Task;
use App\Models\Project;
use App\Http\Resources\CommentResource;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\CommentCreated;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Comment::class);
        $query = Comment::query()->with(['user', 'commentable']);
        if ($request->has('task_id')) {
            $query->where('commentable_type', Task::class)
                  ->where('commentable_id', $request->input('task_id'));
        } elseif ($request->has('project_id')) {
            $query->where('commentable_type', Project::class)
                  ->where('commentable_id', $request->input('project_id'));
        }
        $comments = $query->orderByDesc('created_at')->paginate(15);
        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request)
    {
        $this->authorize('create', Comment::class);
        $data = $request->validated();
        $user = $request->user();
        $commentableType = $data['commentable_type'];
        $commentableId = $data['commentable_id'];
        if (!in_array($commentableType, [Task::class, Project::class])) {
            return $this->errorResponse('Invalid commentable type', 422);
        }
        $comment = Comment::create([
            'user_id' => $user->id,
            'commentable_id' => $commentableId,
            'commentable_type' => $commentableType,
            'content' => $data['content'],
        ]);
        $comment->load(['user', 'commentable']);
        event(new CommentCreated($comment));
        return $this->successResponse(new CommentResource($comment), 'Comment created successfully', 201);
    }

    public function storeForTask(StoreCommentRequest $request, \App\Models\Task $task)
    {
        $this->authorize('create', \App\Models\Comment::class);
        $comment = \App\Models\Comment::create([
            'user_id' => $request->user()->id,
            'commentable_id' => $task->id,
            'commentable_type' => \App\Models\Task::class,
            'content' => $request->input('content'),
        ]);
        $comment->load(['user', 'commentable']);
        event(new \App\Events\CommentCreated($comment));
        return $this->successResponse(new CommentResource($comment), 'Comment created successfully', 201);
    }

    public function storeForProject(StoreCommentRequest $request, \App\Models\Project $project)
    {
        $this->authorize('create', \App\Models\Comment::class);
        $comment = \App\Models\Comment::create([
            'user_id' => $request->user()->id,
            'commentable_id' => $project->id,
            'commentable_type' => \App\Models\Project::class,
            'content' => $request->input('content'),
        ]);
        $comment->load(['user', 'commentable']);
        event(new \App\Events\CommentCreated($comment));
        return $this->successResponse(new CommentResource($comment), 'Comment created successfully', 201);
    }

    public function show(Comment $comment)
    {
        $this->authorize('view', $comment);
        $comment->load(['user', 'commentable']);
        return new CommentResource($comment);
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $comment->update($request->validated());
        $comment->load(['user', 'commentable']);
        return $this->successResponse(new CommentResource($comment), 'Comment updated successfully');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return $this->successResponse(null, 'Comment deleted', 204);
    }
}
