<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmEmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Response as ScrambleResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     *  register a new user
     */
    #[Group('Authentication')]
    #[BodyParameter('name', description: 'Full name of the user', type: 'string', example: 'John Doe')]
    #[BodyParameter('email', description: 'Valid email address', type: 'string', format: 'email', example: 'john@example.com')]
    #[BodyParameter('password', description: 'Password for the account', type: 'string', format: 'password', example: 'secret123')]
    #[BodyParameter('password_confirmation', description: 'Password confirmation', type: 'string', format: 'password', example: 'secret123')]
    #[BodyParameter('role', description: 'User role', type: 'string', example: 'customer')]
    #[ScrambleResponse(201, description: 'User registered successfully', type: 'array{user: array{id: int, name: string, email: string, role: string, created_at: string|null}, token: string}')]
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
    #[Group('Authentication')]
    #[BodyParameter('email', description: 'Email address', type: 'string', format: 'email', example: 'john@example.com')]
    #[BodyParameter('password', description: 'Account password', type: 'string', format: 'password', example: 'secret123')]
    #[ScrambleResponse(200, description: 'Login successful', type: 'array{message: string, user: array{id: int, name: string, email: string, role: string, created_at: string|null}, token: string}')]
    #[ScrambleResponse(401, description: 'Invalid login credentials', type: 'array{message: string}')]
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'welcome back '.auth()->user()->name,
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 200);
    }

    /**
     * sign out of the system
     */
    #[Group('Authentication')]
    #[ScrambleResponse(200, description: 'User logged out successfully', type: 'array{message: string}')]
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * admin endpoint for user's email confirmation
     */
    #[Group('Authentication')]
    #[QueryParameter('email', description: 'Email address to confirm', type: 'string', format: 'email', example: 'admin@example.com')]
    #[ScrambleResponse(200, description: 'Email confirmed successfully', type: 'array{message: string}')]
    #[ScrambleResponse(401, description: 'Unauthenticated', type: 'array{message: string}')]
    #[ScrambleResponse(403, description: 'Only admin users can confirm emails', type: 'array{message: string}')]
    #[ScrambleResponse(422, description: 'The provided email is invalid or cannot be confirmed', type: 'array{message: string}')]
    public function confirmEmail(ConfirmEmailRequest $request)
    {
        $this->authService->confirmEmail($request->validated('email'));

        return response()->json(['message' => 'Email confirmed successfully'], 200);
    }
}
