<?php

namespace App\Services;

use App\Models\Task;
use Kreait\Firebase\Firestore;

class TaskService
{
    public function __construct(private Firestore $firestore) {}

    public function all(string $userId): array
    {
        $tasks = [];

        $docs = $this->firestore->database()
            ->collection('tasks')
            ->where('userId', '=', $userId)
            ->orderBy('createdAt', 'DESC')
            ->documents();

        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $task[] = Task::fromFirestore($doc, $doc->id());
            }
        }

        return $tasks;
    }

    public function update(string $id, array $data): ?Task
    {
        $docRef = $this->firestore->database()->collection('tasks')->document($id);
        if (!$docRef->snapshot()->exists()) return null;

        $docRef->update($data);
        $updated = $docRef->snapshot();

        return Task::fromFirestore($updated->data(), $updated->id());
    }

    public function delete(string $id): void
    {
        $this->firestore->database()->collection('tasks')->document($id)->delete();
    }
}