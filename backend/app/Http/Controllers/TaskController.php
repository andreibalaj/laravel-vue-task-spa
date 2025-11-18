<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(): JsonResponse
    {
        $userId = request()->header('X-User-ID');
        if (!$userId) {
            return response()->json(['error' => 'Missing X-User-ID'], 400);
        }

        return response()->json($this->taskService->all($userId));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $userId = request()->header('X-User-ID');
        if (!$userId) {
            return response()->json(['error' => 'Missing X-User-ID'], 400);
        }

        $task = $this->taskService->create([
            'user_id' => $userId,
            'title' => $request->title,
        ]);

        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $userId = request()->header('X-User-ID');
        if (!$userId) {
            return response()->json(['error' => 'Missing X-User-ID'], 400);
        }

        $task = $this->taskService->update($id, array_merge($request->validated(), ['user_id' => $userId]));
        return $task ? response()->json($task) : response()->noContent();
    }

    public function destroy(string $id): JsonResponse
    {
        $userId = request()->header('X-User-ID');
        if (!$userId) {
            return response()->json(['error' => 'Missing X-User-ID'], 400);
        }

        $success = $this->taskService->delete($id, $userId);
        if ($success) {
            return response()->json(null, 204); // JSON null with 204 status
        }

        return response()->json(['error' => 'Task not found or access denied'], 404);
    }
}
