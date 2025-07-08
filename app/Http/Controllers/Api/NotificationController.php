<?php
 
 namespace App\Http\Controllers\Api;
 
 use App\Models\Notification;
 use App\Services\NotificationService;
 use App\Http\Resources\NotificationResource;
 use App\Http\Requests\Notification\StoreNotificationRequest;
 use App\Http\Requests\Notification\UpdateNotificationRequest;
 use Illuminate\Http\Request;
 use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->middleware('auth:sanctum');
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('viewAny', Notification::class);
        $notifications = $this->service->list();
        return NotificationResource::collection($notifications);
    }

    public function store(StoreNotificationRequest $request)
    {
        try {
            $this->authorize('create', Notification::class);
            $notification = $this->service->create($request->validated());
            $notification->load('user');
            return $this->successResponse(new NotificationResource($notification), 'Notification created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Notification $notification)
    {
        $this->authorize('view', $notification);
        $notification->load('user');
        return new NotificationResource($notification);
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        try {
            $this->authorize('update', $notification);
            $updated = $this->service->update($notification, $request->validated());
            $updated->load('user');
            return $this->successResponse(new NotificationResource($updated), 'Notification updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy(Notification $notification)
    {
        try {
            $this->authorize('delete', $notification);
            $this->service->delete($notification);
            return $this->successResponse(null, 'Notification deleted', 204);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Mark a notification as read.
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Notification $notification)
    {
        try {
            $this->authorize('update', $notification);
            $updated = $this->service->markAsRead($notification);
            $updated->load('user');
            return $this->successResponse(new NotificationResource($updated), 'Notification marked as read');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Mark all notifications as read for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            $count = $this->service->markAllAsRead(auth()->id());
            return $this->successResponse(['count' => $count], "Marked {$count} notifications as read");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}