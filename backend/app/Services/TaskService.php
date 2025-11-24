<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;

class TaskService
{
    private FirestoreClient $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT')
        ]);
    }

    /**
     * Get all tasks for a specific user
     *
     * @param string $userId The ID of the user to fetch tasks for
     * @return array List of tasks with id, userId, title, completed, and createdAt
     */
    public function all(string $userId): array
    {
        $tasks = [];
        $docs = $this->firestore->collection('tasks')
            ->where('userId', '=', $userId)
            ->orderBy('createdAt', 'DESC')
            ->documents();

        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $tasks[] = [
                    'id' => $doc->id(),
                    'userId' => $doc['userId'],
                    'title' => $doc['title'],
                    'completed' => $doc['completed'] ?? false,
                    'createdAt' => $doc['createdAt'] ?? null,
                ];
            }
        }
        return $tasks;
    }

    /**
     * Create a new task
     *
     * @param array $data Task data containing 'user_id' and 'title'
     * @return array The created task with id, userId, title, completed, and createdAt
     */
    public function create(array $data): array
    {
        $task = [
            'userId' => $data['user_id'],
            'title' => $data['title'],
            'completed' => false,
            'createdAt' => new \DateTimeImmutable(),
        ];

        $docRef = $this->firestore->collection('tasks')->newDocument();
        $docRef->set($task);
        $snapshot = $docRef->snapshot();

        return [
            'id' => $snapshot->id(),
            'userId' => $snapshot['userId'],
            'title' => $snapshot['title'],
            'completed' => $snapshot['completed'],
            'createdAt' => $snapshot['createdAt'],
        ];
    }

    /**
     * Update an existing task
     *
     * @param string $id The Firestore document ID of the task to update
     * @param array $data The fields to update (title, completed, etc.)
     * @return array|null Updated task data or null if not found/unauthorized
     */
    public function update(string $id, array $data): ?array
    {
        $docRef = $this->firestore->collection('tasks')->document($id);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists() || $snapshot['userId'] !== ($data['user_id'] ?? '')) {
            return null;
        }

        $updates = [];

        foreach ($data as $field => $value) {
            if ($field === 'user_id') {
                continue;
            }
            $updates[] = ['path' => $field, 'value' => $value];
        }

        if (!empty($updates)) {
            $docRef->update($updates);
        }

        $updated = $docRef->snapshot();
        return [
            'id' => $updated->id(),
            'userId' => $updated['userId'],
            'title' => $updated['title'],
            'completed' => $updated['completed'],
            'createdAt' => $updated['createdAt'],
        ];
    }

    /**
     * Delete a task
     *
     * @param string $id The Firestore document ID of the task to delete
     * @param string $userId The ID of the user requesting deletion
     * @return bool True if deleted successfully, false if not found/unauthorized
     */
    public function delete(string $id, string $userId): bool
    {
        $docRef = $this->firestore->collection('tasks')->document($id);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists() || $snapshot['userId'] !== $userId) {
            return false;
        }

        $docRef->delete();
        return true;
    }
}