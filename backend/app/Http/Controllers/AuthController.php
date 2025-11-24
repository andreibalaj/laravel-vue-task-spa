<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private FirestoreClient $firestore;
    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'projectId' => env('GOOGLE_CLOUD_PROJECT')
        ]);
    }
    public function register(RegisterRequest $request): JsonResponse
    {
        $email = $request->email;

        // Check if email exists
        $users = $this->firestore->collection('users')
            ->where('email', '=', $email)
            ->documents();

        if (!$users->isEmpty()) {
            return response()->json(['error' => 'Email already taken'], 422);
        }

        // Create user
        $userRef = $this->firestore->collection('users')->newDocument();
        $userRef->set([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'created_at' => new \DateTimeImmutable(),
        ]);

        return response()->json([
            'id' => $userRef->id(),
            'name' => $request->name,
            'email' => $email
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {

        $email = $request->email;
        $password = $request->password;

        $documents = $this->firestore->collection('users')
            ->where('email', '=', $email)
            ->documents();

        if ($documents->isEmpty()) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        foreach ($documents as $document) {
            if ($document->exists()) {
                $user = $document->data();
                $user['id'] = $document->id();
                break;
            }
        }

        if (!Hash::check($password, $user['password'])) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        unset($user['password']); // remove user hashed password from the response

        return response()->json($user);
    }
}
