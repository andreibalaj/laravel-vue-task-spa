<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;

class TaskService
{
    private FirestoreClient $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreClient();
    }

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

    public function update(string $id, array $data): ?array
    {
        $docRef = $this->firestore->collection('tasks')->document($id);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists() || $snapshot['userId'] !== ($data['user_id'] ?? '')) {
            return null;
        }

        $updates = [];
        if (isset($data['title'])) $updates['title'] = $data['title'];
        if (isset($data['completed'])) $updates['completed'] = $data['completed'];

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
