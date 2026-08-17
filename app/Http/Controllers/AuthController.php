<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class AuthController extends Controller
{
     public function __construct(
        protected AuthService $authService
    ) {
    }

    /**
     *  register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    /**
     *  sign in to the system
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'welcome back '. auth()->user()->name,
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 200);
    }

    /**
     * sign out of the system
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * admin endpoint for user's email confirmation
     */
    public function confirmEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            
            $this->authService->confirmEmail($request['email']);
        
        } catch(InvalidArgumentException $e){
            return response()->json(['message' => $e->getMessage()], 422);
        }
        return response()->json(['message' => 'Email confirmed successfully'], 200);
    }
}
