<?php

namespace App\Http\Controllers\Api;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\Project;
use App\Models\Comment;
use App\Http\Resources\AttachmentResource;
use App\Http\Requests\Attachment\StoreAttachmentRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function storeForTask(StoreAttachmentRequest $request, Task $task)
    {
        $this->authorize('create', [Attachment::class, $task]);
        $file = $request->file('file');
        $path = $file->store('attachments/tasks/' . $task->id, 'private');
        $attachment = Attachment::create([
            'path' => $path,
            'disk' => 'private',
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'attachable_id' => $task->id,
            'attachable_type' => Task::class,
        ]);
        return $this->successResponse(new AttachmentResource($attachment), 'Attachment uploaded successfully', 201);
    }

    public function storeForProject(StoreAttachmentRequest $request, Project $project)
    {
        $this->authorize('create', [Attachment::class, $project]);
        $file = $request->file('file');
        $path = $file->store('attachments/projects/' . $project->id, 'private');
        $attachment = Attachment::create([
            'path' => $path,
            'disk' => 'private',
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'attachable_id' => $project->id,
            'attachable_type' => Project::class,
        ]);
        return $this->successResponse(new AttachmentResource($attachment), 'Attachment uploaded successfully', 201);
    }

    public function storeForComment(StoreAttachmentRequest $request, Comment $comment)
    {
        $this->authorize('create', [Attachment::class, $comment]);
        $file = $request->file('file');
        $path = $file->store('attachments/comments/' . $comment->id, 'private');
        $attachment = Attachment::create([
            'path' => $path,
            'disk' => 'private',
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'attachable_id' => $comment->id,
            'attachable_type' => Comment::class,
        ]);
        return $this->successResponse(new AttachmentResource($attachment), 'Attachment uploaded successfully', 201);
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);
        if ($attachment->path && Storage::disk($attachment->disk)->exists($attachment->path)) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        }
        $attachment->delete();
        return $this->successResponse(null, 'Attachment deleted', 204);
    }

    public function show(Attachment $attachment)
    {
        $this->authorize('view', $attachment);
        if (!Storage::disk($attachment->disk)->exists($attachment->path)) {
            return $this->errorResponse('File not found', 404);
        }
        return response()->download(storage_path('app/' . $attachment->path), $attachment->file_name, [
            'Content-Type' => $attachment->mime_type,
        ]);
    }
}